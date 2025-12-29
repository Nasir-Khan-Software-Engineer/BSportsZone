<?php
namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerReport implements FromArray, WithHeadings, WithEvents
{
    public function __construct($data)
    {
        $this->data = $data['customerData'];
        $this->companyName = $data['companyName'];
        $this->rptName = "Customer Report";
        $this->from = $data['fromDate'];
        $this->to = $data['toDate'];
        $this->customerType = $data['customerType'] ?? 'all';
        $this->generatedAt = $data['reportGenerationDateTime'];
        $this->posid = $data['posid'];
    }

    public function array(): array
    {
        $result = collect($this->data)->map(function ($item) {
            return [
                $item['customer_id'] ?? '',
                $item['customer_name'] ?? '',
                $item['phone'] ?? '',
                $item['total_sales'] ?? 0,
                $item['total_quantity'] ?? 0,
                $item['total_spending'] ?? '',
                $item['total_discount_amount'] ?? '',
                $item['total_adjustment_amount'] ?? '',
                $item['formatted_last_visited_date'] ?? '-',
                $item['customer_type'] ?? '',
            ];
        })->toArray();

        return $result;
    }

    public function headings(): array
    {
        $headings = [
            [$this->companyName],
            [$this->rptName],
            [],
            ['POSID: ', $this->posid],
            ['From:', $this->from, 'To:', $this->to],
        ];

        if ($this->customerType !== 'all') {
            $headings[] = ['Customer Type:', $this->customerType];
        }

        $headings[] = ['Report Generated At: ', $this->generatedAt];
        $headings[] = [];
        $headings[] = ['Customer Type Definitions:'];
        $headings[] = ['New Customer:', 'A customer who has taken exactly one service in their lifetime, and that service was taken within the last three months.'];
        $headings[] = ['Regular Customer:', 'A customer who has taken multiple services and has taken at least one service within the last three months.'];
        $headings[] = ['Returning Customer:', 'A customer who has taken multiple services in their lifetime.'];
        $headings[] = ['Old Customer:', 'A customer who has taken at least one service in their lifetime but has not taken any services within the last three months.'];
        $headings[] = ['Inactive Customer:', 'A customer who has not taken any services in their lifetime.'];
        $headings[] = ['Note:', 'Customer type is determined based on lifetime purchases, not filtered by date range.'];
        $headings[] = [];
        $headings[] = ['Customer ID', 'Customer Name', 'Phone', 'Total Sales', 'Total Quantity', 'Total Spending', 'Total Discount Amount', 'Total Adjustment Amount', 'Last Visited Date', 'Type'];

        return $headings;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Merge and format header
                $sheet->mergeCells('A1:J1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                $sheet->mergeCells('A2:J2');
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

                // Format customer type definitions
                $defRow = $this->customerType !== 'all' ? 9 : 8;
                $sheet->getStyle("A{$defRow}")->getFont()->setBold(true)->setSize(11);
                $sheet->getStyle("A{$defRow}:B{$defRow}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFE9ECEF');

                // Format table header
                $headerRow = $defRow + 6;
                $sheet->getStyle("A{$headerRow}:J{$headerRow}")->getFont()->setBold(true);
                $sheet->getStyle("A{$headerRow}:J{$headerRow}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFE9ECEF');
            }
        ];
    }
}

