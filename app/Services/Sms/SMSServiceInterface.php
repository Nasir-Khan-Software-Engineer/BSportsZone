<?php

namespace App\Services\Sms;

interface SMSServiceInterface
{
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
    ): bool;
}

