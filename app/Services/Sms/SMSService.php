<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\SmsHistory;
use App\Models\SmsConfig;
use App\Services\Sms\SmsTemplateBuilder;


class SMSService implements SMSServiceInterface
{
    protected $apiUrl;
    protected $username;
    protected $apiKey;
    protected $senderId;
    protected $campaignId;
    protected $templateBuilder;

    public function __construct(SmsTemplateBuilder $templateBuilder)
    {
        $this->templateBuilder = $templateBuilder;
        $this->loadConfigFromSession();
    }

    /**
     * Load SMS configuration from session or database
     */
    protected function loadConfigFromSession(): void
    {
        $smsConfig = session('sms_config');
        
        if ($smsConfig) {
            $this->apiUrl = $smsConfig['base_url'] ?? config('sms.base_url');
            $this->username = $smsConfig['username'] ?? config('sms.username');
            $this->apiKey = $smsConfig['api_key'] ?? config('sms.apikey');
            $this->senderId = $smsConfig['sender_id'] ?? config('sms.sender');
            $this->campaignId = $smsConfig['campaign_id'] ?? config('sms.campaign_id', 'null');
        } else {
            // Fallback to config if session not available (for jobs/queues)
            $this->apiUrl = config('sms.base_url');
            $this->username = config('sms.username');
            $this->apiKey = config('sms.apikey');
            $this->senderId = config('sms.sender');
            $this->campaignId = config('sms.campaign_id', 'null');
        }
    }

    /**
     * Load SMS configuration from database for a specific POS
     */
    protected function loadConfigFromDatabase(int $posId): void
    {
        $smsConfig = SmsConfig::where('posid', $posId)
            ->where('is_active', true)
            ->first();

        if ($smsConfig) {
            $this->apiUrl = $smsConfig->base_url;
            $this->username = $smsConfig->username;
            $this->apiKey = $smsConfig->api_key;
            $this->senderId = $smsConfig->sender_id;
            $this->campaignId = $smsConfig->campaign_id ?? 'null';
        } else {
            // Fallback to config file
            $this->apiUrl = config('sms.base_url');
            $this->username = config('sms.username');
            $this->apiKey = config('sms.apikey');
            $this->senderId = config('sms.sender');
            $this->campaignId = config('sms.campaign_id', 'null');
        }
    }

    /**
     * Send SMS message
     *
     * @param string $phone Phone number (without country code)
     * @param string $message SMS message body
     * @param string $type Transaction type (T = Transactional, P = Promotional, D = Dynamic)
     * @param int $posId POS ID
     * @param string $from Source/module that triggered the SMS (e.g., POS_SALE, DUE_REMINDER)
     * @param array $templateData Optional data for template building (e.g., invoice number, amounts)
     * @return bool Success status
     */
    public function send(
        string $phone,
        string $message,
        string $type,
        int $posId,
        string $from,
        array $templateData = []
    ): bool {
        // Build final message using template if template data is provided
        if (!empty($templateData) && isset($templateData['system_line'])) {
            $message = $this->templateBuilder->build($posId, $templateData['system_line']);
        }

        // Reload config - try session first, then database (for queue jobs)
        if (session()->has('sms_config')) {
            $this->loadConfigFromSession();
        } else {
            $this->loadConfigFromDatabase($posId);
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($this->apiUrl . '/SMS', [
            "UserName"        => $this->username,
            "Apikey"          => $this->apiKey,
            "MobileNumber"    => '88' . $phone,
            "CampaignId"      => "null",
            "SenderName"      => $this->senderId,
            "TransactionType" => $type, // T = Transactional, P = Promotional, D = Dynamic
            "Message"         => $message,
        ]);

        $responseJson = $response->json();
        $messageLength = mb_strlen($message);
        $smsCount = $this->templateBuilder->calculateSmsCount($message);
        
        $logContext = [
            'channel'   => 'sms',
            'phone'     => maskPhone($phone),
            'status'    => $responseJson['status'] ?? null,
            'code'      => $responseJson['statusCode'] ?? null,
            'trxn_id'   => $responseJson['trxnId'] ?? null,
            'result'    => $responseJson['responseResult'] ?? null,
            'from'      => $from,
            'posid'     => $posId,
        ];

        if (($responseJson['statusCode'] ?? null) !== '200') {
            Log::error('SMS_SEND_FAILED', $response->json());
            return false;
        }

        // Log successful SMS to database
        try {
            SmsHistory::create([
                'posid' => $posId,
                'to_number' => $phone,
                'from_number' => $this->senderId,
                'source' => $from,
                'message_length' => $messageLength,
                'sms_count' => $smsCount,
            ]);
        } catch (\Exception $e) {
            Log::warning('SMS_HISTORY_SAVE_FAILED', [
                'error' => $e->getMessage(),
                'posid' => $posId,
                'phone' => maskPhone($phone),
            ]);
        }

        Log::info('SMS_SENT_SUCCESSFULLY', $logContext);

        return true;
    }

    /**
     * Legacy method for backward compatibility
     * @deprecated Use send() instead
     */
    public function sendSMS($phone, $message, $type, $posId, $salesId)
    {
        return $this->send($phone, $message, $type, $posId, 'POS_SALE', []);
    }
}
