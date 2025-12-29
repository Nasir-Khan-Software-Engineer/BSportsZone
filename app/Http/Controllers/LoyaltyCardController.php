<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Loyalty\ILoyaltyService;
use App\Models\Customer;
use App\Models\LoyaltyCard;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Exception;
use App\Models\LoyaltyHistory;
use Carbon\Carbon;

class LoyaltyCardController extends Controller
{
    protected $loyaltyService;

    public function __construct(ILoyaltyService $loyaltyService)
    {
        $this->loyaltyService = $loyaltyService;
    }
    
    public function loyalty($customerId)
    {
        if(!isFeatureEnabled('ENABLED_LOYALTY')) {
            abort(403, 'Loyalty feature is not enabled.');
        }

        $posid = auth()->user()->posid;
        $loyaltyStatus = $this->loyaltyService->getCustomerLoayltyStatus($posid, $customerId);
        $LoyaltyCards = $this->loyaltyService->getCustomerCardsWithStatus($posid, $customerId);

        return view('sales.customer.loyalty.details', ['loyaltyStatus' => $loyaltyStatus, 'loyaltyCards' => $LoyaltyCards, 'customerId' => $customerId]);
    }

    public function getLoyaltyStatus($customerId)
    {
         // Guard feature
        if (!isFeatureEnabled('ENABLED_LOYALTY')) {
            return response()->json([
                'status' => 'error',
                'errors' => ['feature' => ['Loyalty program feature is not enabled.']]
            ], 403);
        }

        try {
            $posid = auth()->user()->posid;
            $statusData = $this->loyaltyService->getCustomerLoayltyStatus($posid, $customerId);

            return response()->json([
                'status' => 'success',
                'loyaltyInfo' => $statusData
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch loyalty status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        // Guard feature
        if (!isFeatureEnabled('ENABLED_LOYALTY')) {
            return response()->json([
                'status' => 'error',
                'errors' => ['feature' => ['Loyalty program feature is not enabled.']]
            ], 403);
        }

        $posid = auth()->user()->posid;
        $statusData = $this->loyaltyService->getCustomerLoayltyStatus($posid, $request->input('customer_id'));

        if($statusData['isEligibleForNewCard'] == false) {
            return response()->json([
                'status' => 'error',
                'errors' => ['eligibility' => ['Customer is not eligible for a new loyalty card. Or Customer already has a loyalty card.']]
            ]);
        }
        
        $validator = Validator::make($request->all(), [
            'customer_id' => ['required','integer','exists:customers,id'],
            'card_number' => [
                'required',
                'digits_between:11,20'
            ],
            'valid_until' => ['required','date','after:today'],
        ], [
            'card_number.digits_between' => 'Card number must be between 11 and 20 digits.',
            'valid_until.after' => 'Valid until must be greater than today.',
        ]);

        // Add unique-per-posid check manually
        $validator->after(function ($validator) use ($request, $posid) {
            $exists = LoyaltyCard::where('posid', $posid)
                ->where('card_number', $request->input('card_number'))
                ->exists();

            if ($exists) {
                $validator->errors()->add('card_number', 'This card number already exists for this Account.');
            }
        });

        // ✅ Validity Period Check (Using Session)
        $validator->after(function ($validator) use ($request) {
            $settings = session('loyaltySettings');
            $validityMonths = $settings['validity_period_months'];

            if (!empty($validityMonths)) {
                $validUntil = Carbon::parse($request->input('valid_until'));
                $maxValidDate = now()->copy()->addMonths($validityMonths);

                if ($validUntil->greaterThan($maxValidDate)) {
                    $validator->errors()->add('valid_until', 
                        "Valid until date cannot exceed {$maxValidDate->format('Y-m-d')} based on {$validityMonths} months validity period."
                    );
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()->toArray()
            ]);
        }

        // Save in transaction
        try {
            $card = LoyaltyCard::create([
                'customer_id' => $request->input('customer_id'),
                'posid' => $posid,
                'card_number' => $request->input('card_number'),
                'valid_until' => $request->input('valid_until'),
                'created_by' => auth()->user()->id
            ]);

            Customer::where('id', $request->input('customer_id'))
                    ->where('posid', $posid)
                    ->update(['latest_card_id' => $card->id, 'type' => 'Loyal']);

            return response()->json([
                'status' => 'success',
                'message' => 'Loyalty card added successfully.',
                'data' => $card
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'status' => 'error',
                'errors' => ['server' => ['Failed to create loyalty card.']]
            ]);
        }
    }

    public function verifyCardAndGetHistory(Request $request)
    {
        // Guard the feature
        if (!isFeatureEnabled('ENABLED_LOYALTY')) {
            return response()->json([
                'status' => 'error',
                'errors' => ['feature' => ['Loyalty program feature is not enabled.']]
            ]);
        }

        $validator = Validator::make($request->all(), [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'card_number' => ['required', 'digits_between:11,20']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()->toArray()
            ]);
        }

        try {
            $posid = auth()->user()->posid;
            $customerId = $request->input('customer_id');
            $cardNumber = $request->input('card_number');
            
            $cardData = $this->loyaltyService->getCustomerCardStatusByCardNumber($posid, $customerId, $cardNumber);

            if (!$cardData) {
                return response()->json([
                    'status' => 'error',
                    'errors' => ['card_number' => ['Card not found.']]
                ]);
            }

            $response = [
                'status' => 'success',
                'card' => $cardData,
            ];

            return response()->json($response);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to verify card.',
                'errors' => ['server' => [$e->getMessage()]]
            ]);
        }
    }

    public function getCardHistory($cardId)
    {
        // Guard the feature
        if (!isFeatureEnabled('ENABLED_LOYALTY')) {
            return response()->json([
                'status' => 'error',
                'errors' => ['feature' => ['Loyalty program feature is not enabled.']]
            ]);
        }

        try {
            $posid = auth()->user()->posid;
            $history = $this->loyaltyService->getCardHistory($posid, $cardId);

            return response()->json([
                'status' => 'success',
                'data' => $history
            ]);

        } catch (Exception $e) {
            \Log::error("Card history error for card {$cardId}: " . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to fetch card history'
            ], 500);
        }
    }

    public function update(Request $request, $cardId)
    {
        // Guard the feature
        if (!isFeatureEnabled('ENABLED_LOYALTY')) {
            return response()->json([
                'status' => 'error',
                'errors' => ['feature' => ['Loyalty program feature is not enabled.']]
            ]);
        }

        $card = LoyaltyCard::where('id', $cardId)->firstOrFail();
        $compareDate = $card->created_at->format('Y-m-d');

        $validator = Validator::make($request->all(), [
            'card_number' => ['required', 'digits_between:11,20'],
            'valid_until' => ['required', 'date', 'after:' . $compareDate],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()->toArray()
            ]);
        }
            

        try {
            $posid = auth()->user()->posid;
            $cardNumber = $request->input('card_number');
            $valid_until = Carbon::parse($request->input('valid_until'));
            $settings = session('loyaltySettings');
            $validityMonths = $settings['validity_period_months'];

            if (!empty($validityMonths)) {
                $maxValidDate = $card->created_at->copy()->addMonths($validityMonths);

                if ($valid_until->greaterThan($maxValidDate)) {
                    return response()->json([
                        'status' => 'error',
                        'errors' => [
                            'valid_until' => ["Valid until date cannot exceed {$maxValidDate->format('Y-m-d')} based on {$validityMonths} months validity period."]
                        ]
                    ]);
                }
            }
            
            $updatedCard = $this->loyaltyService->updateCard($cardId, $cardNumber, $valid_until);

            return response()->json([
                'status' => 'success',
                'message' => 'Card updated successfully.',
                'data' => $updatedCard
            ]);

        } catch (Exception $e) {
            \Log::error("Card update error for card {$cardId}: " . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to update card'
            ], 500);
        }
    }
}
