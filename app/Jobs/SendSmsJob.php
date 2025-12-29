<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Sms\SMSServiceInterface;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phone;
    protected $message;
    protected $type;
    protected $posId;
    protected $from;
    protected $templateData;

    /**
     * Create a new job instance.
     *
     * @param string $phone Phone number (without country code)
     * @param string $message SMS message body (or empty if using template)
     * @param string $type Transaction type (T = Transactional, P = Promotional, D = Dynamic)
     * @param int $posId POS ID
     * @param string $from Source/module that triggered the SMS (e.g., POS_SALE, DUE_REMINDER)
     * @param array $templateData Optional data for template building (e.g., invoice number, amounts)
     */
    public function __construct($phone, $message, $type, $posId, $from = 'POS_SALE', $templateData = [])
    {
        $this->phone = $phone;
        $this->message = $message;
        $this->type = $type;
        $this->posId = $posId;
        $this->from = $from;
        $this->templateData = $templateData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SMSServiceInterface $smsService)
    {
        $smsService->send(
            $this->phone,
            $this->message,
            $this->type,
            $this->posId,
            $this->from,
            $this->templateData
        );
    }
}
