<?php

namespace App\Services\Purchase;

use App\Repositories\Purchase\IPurchaseRepository;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\DB;

class PurchaseService implements IPurchaseService
{
    public function __construct(IPurchaseRepository $purchaseRepository)
    {
        $this->purchaseRepository = $purchaseRepository;
    }

    public function getAllPurchases($POSID, $search = '', $start = 0, $length = 10, $orderColumn = 0, $orderDir = 'desc')
    {
        return $this->purchaseRepository->getAllPurchases($POSID, $search, $start, $length, $orderColumn, $orderDir);
    }

    public function getPurchaseById($POSID, $id)
    {
        return $this->purchaseRepository->getPurchaseById($POSID, $id);
    }

    public function getPurchaseWithItems($POSID, $id)
    {
        return $this->purchaseRepository->getPurchaseWithItems($POSID, $id);
    }

    public function savePurchase($purchaseData, $purchaseItems)
    {
        return DB::transaction(function () use ($purchaseData, $purchaseItems) {
            // Calculate totals
            $totalQty = 0;
            $totalCostPrice = 0;

            foreach ($purchaseItems as $item) {
                $totalQty += $item['purchased_qty'];
                $totalCostPrice += $item['cost_price'] * $item['purchased_qty'];
            }

            $purchaseData['total_qty'] = $totalQty;
            $purchaseData['total_cost_price'] = $totalCostPrice;

            // Save purchase
            $purchase = $this->purchaseRepository->savePurchase($purchaseData);

            // Save purchase items
            foreach ($purchaseItems as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_variant_id' => $item['product_variant_id'],
                    'cost_price' => $item['cost_price'],
                    'purchased_qty' => $item['purchased_qty'],
                    'unallocated_qty' => $item['purchased_qty'], // Initially same as purchased_qty
                ]);
            }

            return $purchase->load('purchaseItems.variation');
        });
    }

    public function updatePurchase($purchaseData, $purchaseItems)
    {
        return DB::transaction(function () use ($purchaseData, $purchaseItems) {
            $purchase = $this->purchaseRepository->getPurchaseById($purchaseData['pos_id'], $purchaseData['id']);
            
            if (!$purchase) {
                return null;
            }

            // Check which items are editable (no allocation)
            $existingItems = $purchase->purchaseItems;
            $editableItemIds = [];
            $nonEditableItemIds = [];

            foreach ($existingItems as $item) {
                if ($item->isEditable()) {
                    $editableItemIds[] = $item->id;
                } else {
                    $nonEditableItemIds[] = $item->id;
                }
            }

            // Delete editable items that are not in the new list
            $newItemVariantIds = array_column($purchaseItems, 'product_variant_id');
            foreach ($existingItems as $item) {
                if ($item->isEditable() && !in_array($item->product_variant_id, $newItemVariantIds)) {
                    $item->delete();
                }
            }

            // Calculate totals (include non-editable items)
            $totalQty = 0;
            $totalCostPrice = 0;

            // Add non-editable items to totals
            foreach ($existingItems as $item) {
                if (!$item->isEditable()) {
                    $totalQty += $item->purchased_qty;
                    $totalCostPrice += $item->cost_price * $item->purchased_qty;
                }
            }

            // Process new/updated items
            foreach ($purchaseItems as $item) {
                $existingItem = $existingItems->firstWhere('product_variant_id', $item['product_variant_id']);
                
                if ($existingItem && $existingItem->isEditable()) {
                    // Update existing editable item
                    $updateData = [
                        'cost_price' => $item['cost_price'],
                        'purchased_qty' => $item['purchased_qty'],
                        'unallocated_qty' => $item['purchased_qty'],
                    ];
                    if (isset($item['status'])) {
                        $updateData['status'] = $item['status'];
                    }
                    $existingItem->update($updateData);
                } else {
                    // Create new item
                    $createData = [
                        'purchase_id' => $purchase->id,
                        'product_variant_id' => $item['product_variant_id'],
                        'cost_price' => $item['cost_price'],
                        'purchased_qty' => $item['purchased_qty'],
                        'unallocated_qty' => $item['purchased_qty'],
                    ];
                    if (isset($item['status'])) {
                        $createData['status'] = $item['status'];
                    }
                    PurchaseItem::create($createData);
                }

                $totalQty += $item['purchased_qty'];
                $totalCostPrice += $item['cost_price'] * $item['purchased_qty'];
            }

            $purchaseData['total_qty'] = $totalQty;
            $purchaseData['total_cost_price'] = $totalCostPrice;

            // Update purchase
            $this->purchaseRepository->updatePurchase($purchaseData);

            return $purchase->fresh(['purchaseItems.variation']);
        });
    }

    public function getTotalPurchases($POSID, $search = '')
    {
        return $this->purchaseRepository->getTotalPurchases($POSID, $search);
    }
}

