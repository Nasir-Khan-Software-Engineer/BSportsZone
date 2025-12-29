<?php
namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RevenueReport implements FromArray, WithHeadings, WithEvents
{
    public function __construct($data)
    {
        $this->data = $data['revenueData'];
        $this->companyName = $data['companyName'];
        $this->rptName = "Revenue Report";
        $this->from = $data['fromDate'];
        $this->to = $data['toDate'];
        $this->generatedAt = $data['reportGenerationDateTime'];
        $this->posid = $data['posid'];

        $this->totalQuantity = $data['totals']['totalQuantity'];
        $this->totalRevenue = $data['totals']['totalRevenue'];
    }

    public function array(): array
    {
        $result = collect($this->data)->map(function ($item) {
            return [
                $item['code'] ?? '',
                $item['name'] ?? '',
                $item['price'] ?? '0',
                $item['quantity_sold'] ?? 0,
                $item['revenue'] ?? '0',
            ];
        })->toArray();

        return $result;
    }

    public function headings(): array
    {
        return [
            [$this->companyName],
            [$this->rptName],
            [],
            ['POSID: ', $this->posid],
            ['From:', $this->from, 'To:', $this->to],
            ['Report Generated At: ', $this->generatedAt],
            [],
            ['Code', 'Service Name', 'Price', 'Quantity Sold', 'Revenue'],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Merge and format header
                $sheet->mergeCells('A1:E1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                $sheet->mergeCells('A2:E2');
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

                // Format table header
                $sheet->getStyle('A6:E6')->getFont()->setBold(true);
                $sheet->getStyle('A6:E6')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFE9ECEF');

                // Add totals row
                $lastRow = count($this->data) + 7;
                $sheet->mergeCells("A{$lastRow}:C{$lastRow}");
                $sheet->setCellValue("A{$lastRow}", 'Total');
                $sheet->getStyle("A{$lastRow}")->getFont()->setBold(true);
                $sheet->getStyle("A{$lastRow}")->getAlignment()->setHorizontal('right');
                
                $sheet->setCellValue("D{$lastRow}", $this->totalQuantity);
                $sheet->setCellValue("E{$lastRow}", $this->totalRevenue);
                $sheet->getStyle("D{$lastRow}:E{$lastRow}")->getFont()->setBold(true);
            }
        ];
    }
}

