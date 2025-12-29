<?php

namespace App\Services\Sms;

use App\Models\SmsTemplate;

class SmsTemplateBuilder
{
    /**
     * Placeholder pattern for system-generated content
     */
    const PLACEHOLDER_PATTERN = '/\[##\s*(.*?)\s*##\]/';

    /**
     * Build SMS message from template by replacing placeholder with system-generated content
     *
     * @param int $posId POS ID
     * @param string $systemLine System-generated middle line (e.g., invoice details)
     * @return string Complete SMS message
     */
    public function build(int $posId, string $systemLine): string
    {
        $template = SmsTemplate::where('posid', $posId)->first();
        
        if (!$template || empty($template->template)) {
            // Fallback: return just the system line if no template exists
            return $systemLine;
        }
        
        // Replace the placeholder [## ... ##] with the actual system-generated content
        $message = preg_replace(
            self::PLACEHOLDER_PATTERN,
            $systemLine,
            $template->template
        );
        
        return trim($message);
    }

    /**
     * Build system line for POS sale
     *
     * @param array $data Sale data
     * @return string System-generated line
     */
    public function buildSystemLineForSale(array $data): string
    {
        $parts = [];
        
        if (isset($data['invoice_code'])) {
            $parts[] = 'Inv:' . $data['invoice_code'];
        }
        
        if (isset($data['payable_amount'])) {
            $parts[] = 'Payable:Tk ' . number_format($data['payable_amount'], 0, '.', '');
        }
        
        if (isset($data['quantity'])) {
            $parts[] = 'Qty:' . $data['quantity'];
        }
        
        if (isset($data['discount_amount']) && $data['discount_amount'] > 0) {
            $parts[] = 'Disc:Tk ' . number_format($data['discount_amount'], 0, '.', '');
        }
        
        if (isset($data['adjustment_amount']) && $data['adjustment_amount'] != 0) {
            $parts[] = 'Adj:Tk ' . number_format($data['adjustment_amount'], 0, '.', '');
        }
        
        return implode(' ', $parts);
    }

    /**
     * Calculate SMS count based on message length
     *
     * @param string $message SMS message
     * @return int Number of SMS parts (1 SMS = 160 characters)
     */
    public function calculateSmsCount(string $message): int
    {
        $length = mb_strlen($message);
        return (int) ceil($length / 160);
    }

    /**
     * Validate template contains the required placeholder
     *
     * @param string $template Full SMS template
     * @return array ['valid' => bool, 'has_placeholder' => bool]
     */
    public function validateTemplatePlaceholder(string $template): array
    {
        $hasPlaceholder = preg_match(self::PLACEHOLDER_PATTERN, $template);
        
        return [
            'valid' => $hasPlaceholder === 1,
            'has_placeholder' => $hasPlaceholder === 1,
        ];
    }

    /**
     * Validate template length doesn't exceed SMS limits
     *
     * @param string $template Full SMS template with placeholder
     * @param int $systemLineLength Estimated length of system-generated line (default 80)
     * @return array ['valid' => bool, 'total' => int, 'limit' => int, 'template_length' => int]
     */
    public function validateTemplateLength(string $template, int $systemLineLength = 80): array
    {
        // Replace placeholder with estimated system line length
        $estimatedMessage = preg_replace(
            self::PLACEHOLDER_PATTERN,
            str_repeat('X', $systemLineLength),
            $template
        );
        
        $templateLength = mb_strlen($template);
        $estimatedLength = mb_strlen($estimatedMessage);
        $limit = 160; // Maximum characters per SMS
        
        return [
            'valid' => $estimatedLength <= $limit,
            'total' => $estimatedLength,
            'limit' => $limit,
            'template_length' => $templateLength,
            'estimated_length' => $estimatedLength,
        ];
    }

    /**
     * Extract parts from template for display purposes
     *
     * @param string $template Full SMS template
     * @return array ['header' => string, 'system_placeholder' => string, 'footer' => string]
     */
    public function extractTemplateParts(string $template): array
    {
        $parts = preg_split(self::PLACEHOLDER_PATTERN, $template, -1, PREG_SPLIT_DELIM_CAPTURE);
        
        $header = trim($parts[0] ?? '');
        $systemPlaceholder = isset($parts[1]) ? '[## ' . trim($parts[1]) . ' ##]' : '';
        $footer = trim($parts[2] ?? '');
        
        return [
            'header' => $header,
            'system_placeholder' => $systemPlaceholder,
            'footer' => $footer,
        ];
    }
}

