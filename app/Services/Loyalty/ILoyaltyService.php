<?php
namespace App\Services\Loyalty;

interface ILoyaltyService{
    public function getCustomerLoayltyStatus($posId, $customerId);
    public function getCustomerCardStatusByCardNumber($posId, $customerId, $cardNumber);
    public function getCustomerCardsWithStatus($posId, $customerId);
    public function getCardHistory($posId, $cardId);
    public function updateCard($cardId, $cardNumber, $valid_unitl);
}

