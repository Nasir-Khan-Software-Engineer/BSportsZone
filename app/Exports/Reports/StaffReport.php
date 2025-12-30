<?php
namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StaffReport implements FromArray, WithHeadings, WithEvents
{
    public function __construct($data)
    {
        $this->data = $data['staffData'];
        $this->companyName = $data['companyName'];
        $this->rptName = "Staff Performance Report";
        $this->from = $data['fromDate'];
        $this->to = $data['toDate'];
        $this->generatedAt = $data['reportGenerationDateTime'];
        $this->posid = $data['posid'];
    }

    public function array(): array
    {
        $result = collect($this->data)->map(function ($item) {
            return [
                $item['employee_name'] ?? '',
                $item['phone'] ?? '-',
                $item['total_working_days'] ?? 0,
                $item['present_display'] ?? '0% (0)',
                $item['absent_display'] ?? '0% (0)',
                $item['total_leave'] ?? 0,
                $item['total_review'] ?? 0,
                $item['positive_display'] ?? '0% (0)',
                $item['warning_display'] ?? '0% (0)',
                $item['negative_display'] ?? '0% (0)',
                $item['total_services'] ?? 0,
                $item['avg_services_per_day'] ?? 0,
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
            ['Report Generated At: ', $this->generatedAt],
            [],
            ['Working Days Calculation:'],
            ['Only days with attendance activity (Present, Absent, or Leave) are counted as working days. Days with no attendance records are considered holidays and excluded from the calculation.'],
            [],
            ['Staff Name', 'Phone', 'Total Working Days', 'Present', 'Absent', 'Total Leave', 'Total Review', 'Positive Review', 'Warning Review', 'Negative Review', 'TOT Service', 'AVG Service'],
        ];

        return $headings;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Merge and format header
                $sheet->mergeCells('A1:L1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                $sheet->mergeCells('A2:L2');
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

                // Format working days calculation note
                $noteRow = 8;
                $sheet->getStyle("A{$noteRow}")->getFont()->setBold(true)->setSize(11);
                $sheet->getStyle("A{$noteRow}:L{$noteRow}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFE9ECEF');
                $sheet->mergeCells("A{$noteRow}:L{$noteRow}");
                
                $descRow = $noteRow + 1;
                $sheet->mergeCells("A{$descRow}:L{$descRow}");

                // Format table header
                $headerRow = $descRow + 2;
                $sheet->getStyle("A{$headerRow}:L{$headerRow}")->getFont()->setBold(true);
                $sheet->getStyle("A{$headerRow}:L{$headerRow}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFE9ECEF');
            }
        ];
    }
}

