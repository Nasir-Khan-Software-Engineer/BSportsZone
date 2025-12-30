<?php
namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SmsHistoryReport implements FromArray, WithHeadings, WithEvents
{
    public function __construct($data)
    {
        $this->data = $data['smsData'];
        $this->companyName = $data['companyName'];
        $this->rptName = "SMS History Report";
        $this->from = $data['fromDate'];
        $this->to = $data['toDate'];
        $this->totalSmsCount = $data['totalSmsCount'] ?? 0;
        $this->totalMessageLength = $data['totalMessageLength'] ?? 0;
        $this->totalCost = $data['totalCost'] ?? '0.00';
        $this->generatedAt = $data['reportGenerationDateTime'];
        $this->POSID = $data['POSID'];
    }

    public function array(): array
    {
        $result = collect($this->data)->map(function ($item) {
            return [
                $item['date_time'] ?? '',
                $item['source'] ?? '-',
                $item['from_number'] ?? '-',
                $item['to_number'] ?? '-',
                $item['message_length'] ?? 0,
                $item['sms_count'] ?? 0,
                $item['unit_cost'] ?? '45 poysa',
                $item['total_cost'] ?? '0.00 taka',
            ];
        })->toArray();

        // Add total row
        $result[] = [
            '',
            '',
            '',
            'Total',
            $this->totalMessageLength,
            $this->totalSmsCount,
            '',
            $this->totalCost,
        ];

        return $result;
    }

    public function headings(): array
    {
        $headings = [
            [$this->companyName],
            [$this->rptName],
            [],
            ['POSID: ', $this->POSID],
            ['From:', $this->from, 'To:', $this->to],
        ];

        $headings[] = ['Report Generated At: ', $this->generatedAt];
        $headings[] = [];
        $headings[] = ['Date Time', 'Source', 'From Number', 'To Number', 'SMS Length', 'SMS Count', 'Unit Cost', 'Total Cost'];

        return $headings;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Merge and format header
                $sheet->mergeCells('A1:H1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                $sheet->mergeCells('A2:H2');
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

                // Format table header
                $headerRow = 7;
                $sheet->getStyle("A{$headerRow}:H{$headerRow}")->getFont()->setBold(true);
                $sheet->getStyle("A{$headerRow}:H{$headerRow}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFE9ECEF');

                // Format total row
                $lastRow = count($this->data) + 8;
                $sheet->mergeCells("A{$lastRow}:D{$lastRow}");
                $sheet->setCellValue("A{$lastRow}", 'Total');
                $sheet->getStyle("A{$lastRow}")->getFont()->setBold(true);
                $sheet->getStyle("A{$lastRow}")->getAlignment()->setHorizontal('right');
                $sheet->getStyle("A{$lastRow}:H{$lastRow}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFE9ECEF');
                
                $sheet->setCellValue("E{$lastRow}", $this->totalMessageLength);
                $sheet->setCellValue("F{$lastRow}", $this->totalSmsCount);
                $sheet->setCellValue("H{$lastRow}", $this->totalCost);
                $sheet->getStyle("E{$lastRow}:H{$lastRow}")->getFont()->setBold(true);
            }
        ];
    }
}

