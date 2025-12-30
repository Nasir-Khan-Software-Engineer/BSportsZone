<?php
namespace App\Services\Loyalty;

use App\Services\Loyalty\ILoyaltyService;
use App\Models\Customer;
use App\Models\LoyaltySetting;
use App\Models\LoyaltyCard;
use App\Models\LoyaltyHistory;
use Carbon\Carbon;

class LoyaltyService implements ILoyaltyService{

    public function getCustomerLoayltyStatus($posId, $customerId)
    {
        $customer = Customer::with(['loyaltyCards.histories','sales'])->where('posid', $posId)->find($customerId);

        $settings = session('loyaltySettings');
        $minSales = $settings['minimum_sales_amount'];
        $minSalesAmountAppliesFor = $settings['minimum_sales_amount_applies_for'];

        $latestCard = $customer->loyaltyCards->sortByDesc('created_at')->first();
        $isEligibleForNewCard = false;
        $currentTotalSpent = 0;
        $needForNextCard = 0;
        $visitCount = 0;
        $totalDiscount = 0;
        $status = "No Card";
        $cardStatusData = $this->getCardStatusData(null);

        // CUSTOMER HAS CARD(s)
        if ($latestCard) {
            $cardStatusData = $this->getCardStatusData($latestCard);

            $visitCount = $cardStatusData['visits_used'];
            $status = $cardStatusData['status'];
            $totalDiscount = $latestCard->histories->where('isSkipped', false)->sum('discount_amount');
            
            $nextCardData = $this->getNextCardEligibility($customer, $latestCard);

            $isEligibleForNewCard = $nextCardData['isEligibleForNewCard'];
            $currentTotalSpent = $nextCardData['currentTotalSpent'];
            $needForNextCard = $nextCardData['needForNextCard'];
        }else{
            // CUSTOMER HAS NO CARD YET
            $nextCardData = $this->getNextCardEligibility($customer, $latestCard);
                
            $isEligibleForNewCard = $nextCardData['isEligibleForNewCard'];
            $currentTotalSpent = $nextCardData['currentTotalSpent'];
            $needForNextCard = $nextCardData['needForNextCard'];
        }
        
        return [
            'status' => $status,  // Customer Status (Loyal, Expired, Completed, Limited)
            'isEligibleForNewCard' => $isEligibleForNewCard,
            'currentTotalSpent' => $currentTotalSpent,
            'needForNextCard' => $needForNextCard,
            'minSales' => $minSales,
            'minSalesAmountAppliesFor' => $minSalesAmountAppliesFor,
            'visitCount' => $visitCount,
            'totalDiscount' => $totalDiscount,
            'cardStatus' => $cardStatusData,
            'settings' => $settings
        ];
    }

    public function getCustomerCardsWithStatus($posId, $customerId)
    {
        // Load all cards with their histories in one query
        $loyaltyCards = LoyaltyCard::with('histories')
            ->where('posid', $posId)
            ->where('customer_id', $customerId)
            ->get();

        // Get settings once
        $settings = session('loyaltySettings');
        $maxVisits = $settings['max_visits'];

        // Process each card using Function B
        $cardsWithStatus = $loyaltyCards->map(function ($card){
            return $this->getCardStatusData($card);
        });

        return [
            'customer_id' => $customerId,
            'pos_id' => $posId,
            'total_cards' => $cardsWithStatus->count(),
            'cards' => $cardsWithStatus,
            'settings' => $settings
        ];
    }

    private function getCardStatusData($card = null)
    {
        // Handle empty card for new customers
        if (empty($card) || !$card->exists) {
            return $this->getDemoCardData();
        }

        // Count visits from pre-loaded histories where isSkipped = false
        $visitsCount = $card->histories->where('isSkipped', false)->count();

        $todayVisits = $card->histories()
                        ->whereDate('created_at', today())
                        ->where('isSkipped', false)
                        ->count();

        $loyaltySettings = session('loyaltySettings');
        $max_visits_per_day = $loyaltySettings['max_visits_per_day'];
        $maxVisits = $loyaltySettings['max_visits'];

        // Calculate expiry date
        $validUntil = Carbon::parse($card->valid_until);
        $today = now();

        // Determine status
        if ($today->gt($validUntil)) {
            $status = 'Expired';
        } elseif ($visitsCount >= $maxVisits) {
            $status = 'Completed';
        } else if($todayVisits >= $max_visits_per_day){
            $status = 'Limited';
        } else {
            $status = 'Loyal';
        }

        return [
            'card_id' => $card->id,
            'card_number' => $card->card_number,
            'issued_at' => formatDateAndTime($card->created_at),
            'status' => $status,
            'visits_used' => $visitsCount,
            'visits_remaining' => max(0, $maxVisits - $visitsCount),
            'valid_until' => formatDate($card->valid_until),
            'max_visits' => $maxVisits,
            'card_data' => $card,
            'is_demo' => false
        ];
    }

    /**
     * Get card history with sale data, sorted by sale date (oldest first)
     */
    public function getCardHistory($posId, $cardId)
    {
        $history = LoyaltyHistory::with(['sale'])
            ->where('card_id', $cardId)
            ->where('loyalty_histories.posid', $posId)
            ->join('sales', 'loyalty_histories.sales_id', '=', 'Sales.id')
            ->orderBy('Sales.created_at', 'asc')
            ->select('loyalty_histories.*')
            ->get();

        // Format the data for the table
        $formattedHistory = $history->map(function ($item, $index) {
            return [
                'visit_number' => $index + 1,
                'date' => formatDateAndTime($item->sale->created_at),
                'invoice_no' => $item->sale->invoice_code ?? 'N/A',
                'total_amount' => $item->sale->total_amount ?? '0.00',
                'discount_type' => $item->discount_type ?? 'Fixed',
                'discount' => $item->discount_value ?? '0.00',
                'discount_amount' => $item->discount_amount ?? '0.00',
                'sale_id' => $item->sales_id ?? null,
                'note' => $item->note,
                'isSkipped' => $item->isSkipped
            ];
        });

        return $formattedHistory;
    }

    public function updateCard($cardId, $cardNumber, $valid_unitl)
    {
        $card = LoyaltyCard::find($cardId);
        $card->card_number = $cardNumber;
        $card->valid_until = $valid_unitl;
        $card->updated_by = auth()->user()->id;
        $card->save();

        return $card;
    }

    public function getCustomerCardStatusByCardNumber($posId, $customerId, $cardNumber)
    {
        $card = LoyaltyCard::where('posid', $posId)
            ->where('customer_id', $customerId)
            ->where('card_number', $cardNumber)
            ->first();

        return $this->getCardStatusData($card);
    }

    /**
     * Get demo card data for new customers
    */
    private function getDemoCardData()
    {
        return [
            'card_id' => null,
            'card_number' => '-',
            'issued_at' => '-',
            'status' => 'No Card',
            'visits_used' => 0,
            'visits_remaining' => 0,
            'valid_until' => '-',
            'max_visits' => 0,
            'card_data' => null,
            'is_demo' => true
        ];
    }

    private function getNextCardEligibility($customer, $latestCard = null)
    {
        $settings = session('loyaltySettings');
        $minSales = $settings['minimum_sales_amount'];
        $minSalesAmountAppliesFor = $settings['minimum_sales_amount_applies_for'];
        $isEligibleForNewCard = false;
        $currentTotalSpent = 0;
        $needForNextCard = 0;

        // for no card yet customer, we need to check from the customer created date
        // for already have card, we need to check from the latest card created date
        $comparedDate = $customer->created_at;
        if($latestCard){
            $comparedDate = $latestCard->created_at;
        }

        if($minSalesAmountAppliesFor == "All"){
            // case 1: if all sales sum is minimum sales amount
            $currentTotalSpent = $customer->sales->where('created_at', '>=', $comparedDate)->sum('total_amount');
            $needForNextCard = $minSales - $currentTotalSpent;

            if ($needForNextCard <= 0) {
                $isEligibleForNewCard = true;
            } else {
                $isEligibleForNewCard = false;
            }
            
        }
        else if($minSalesAmountAppliesFor == "Single"){
            // case 2: if one single sale is minimum sales amount

            // sales having the max total amount
            $maxSalesAmount = $customer->sales->where('created_at', '>=', $comparedDate)->max('total_amount');
            $currentTotalSpent = $maxSalesAmount ?? 0;
            $needForNextCard = $minSales - $currentTotalSpent;

            if ($needForNextCard <= 0) {
                $isEligibleForNewCard = true;
            }else{
                $isEligibleForNewCard = false;
                $needForNextCard = $minSales;
            }
        }else{
            $isEligibleForNewCard = false;
        }

        return [
            'isEligibleForNewCard' => $isEligibleForNewCard,
            'currentTotalSpent' => $currentTotalSpent,
            'needForNextCard' => $needForNextCard
        ];
    }
}