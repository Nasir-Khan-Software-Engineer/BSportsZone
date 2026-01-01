<?php

namespace App\Repositories\Purchase;

use App\Models\Purchase;

interface IPurchaseRepository
{
    public function getAllPurchases($POSID, $search = '', $start = 0, $length = 10, $orderColumn = 0, $orderDir = 'desc');
    public function getPurchaseById($POSID, $id);
    public function getPurchaseWithItems($POSID, $id);
    public function savePurchase($purchaseData);
    public function updatePurchase($purchaseData);
    public function getTotalPurchases($POSID, $search = '');
}

