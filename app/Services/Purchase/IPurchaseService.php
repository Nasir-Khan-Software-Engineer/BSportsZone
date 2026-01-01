<?php

namespace App\Services\Purchase;

interface IPurchaseService
{
    public function getAllPurchases($POSID, $search = '', $start = 0, $length = 10, $orderColumn = 0, $orderDir = 'desc');
    public function getPurchaseById($POSID, $id);
    public function getPurchaseWithItems($POSID, $id);
    public function savePurchase($purchaseData, $purchaseItems);
    public function updatePurchase($purchaseData, $purchaseItems);
    public function getTotalPurchases($POSID, $search = '');
}

