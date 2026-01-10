<?php

namespace App\Repositories\Purchase;

use App\Models\Purchase;
use App\Models\PurchaseItem;

class PurchaseRepository implements IPurchaseRepository
{
    public function getAllPurchases($POSID, $search = '', $start = 0, $length = 10, $orderColumn = 0, $orderDir = 'desc')
    {
        $query = Purchase::where('POSID', $POSID)
            ->with(['supplier', 'product', 'creator', 'purchaseItems.variation'])
            ->where(function($q) use ($search) {
                if ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%")
                      ->orWhereHas('product', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('supplier', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                }
            });

        // Handle sorting
        if ($orderColumn == 0) {
            $query->orderBy('id', $orderDir);
        } elseif ($orderColumn == 1) {
            $query->orderBy('purchase_date', $orderDir);
        } elseif ($orderColumn == 2) {
            $query->orderBy('invoice_number', $orderDir);
        } elseif ($orderColumn == 3) {
            $query->orderBy('name', $orderDir);
        } else {
            $query->orderBy('id', 'desc');
        }

        return $query->skip($start)->take($length)->get();
    }

    public function getPurchaseById($POSID, $id)
    {
        return Purchase::where('POSID', $POSID)
            ->where('id', $id)
            ->with(['supplier', 'product', 'creator', 'purchaseItems.variation'])
            ->first();
    }

    public function getPurchaseWithItems($POSID, $id)
    {
        return Purchase::where('POSID', $POSID)
            ->where('id', $id)
            ->with(['supplier', 'product', 'product.variations', 'creator', 'purchaseItems.variation'])
            ->first();
    }

    public function savePurchase($purchaseData)
    {
        return Purchase::create($purchaseData);
    }

    public function updatePurchase($purchaseData)
    {
        $purchase = Purchase::find($purchaseData['id']);
        if ($purchase) {
            unset($purchaseData['id']);
            $purchase->update($purchaseData);
            return $purchase;
        }
        return null;
    }

    public function getTotalPurchases($POSID, $search = '')
    {
        $query = Purchase::where('POSID', $POSID);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhereHas('product', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('supplier', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        return $query->count();
    }
}

