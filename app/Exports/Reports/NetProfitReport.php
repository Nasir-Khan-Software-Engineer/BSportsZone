<?php
namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NetProfitReport implements FromArray, WithHeadings, WithEvents
{
    public function __construct($data)
    {
        $this->data = $data['netProfitData'];
        $this->companyName = $data['companyName'];
        $this->rptName = "Net Profit Report";
        $this->from = $data['fromDate'];
        $this->to = $data['toDate'];
        $this->generatedAt = $data['reportGenerationDateTime'];
        $this->POSID = $data['POSID'];

        $this->totalSalesRevenue = $data['totals']['totalSalesRevenue'];
        $this->totalExpenses = $data['totals']['totalExpenses'];
        $this->totalNetProfit = $data['totals']['totalNetProfit'];
        $this->totalProfitMargin = $data['totals']['totalProfitMargin'];
    }

    public function array(): array
    {
        $result = collect($this->data)->map(function ($item) {
            return [
                $item['formattedDate'] ?? '',
                $item['totalSalesRevenue'] ?? '0',
                $item['totalExpenses'] ?? '0',
                $item['netProfit'] ?? '0',
                $item['profitMargin'] ?? '0%',
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
            ['POSID: ', $this->POSID],
            ['From:', $this->from, 'To:', $this->to],
            ['Report Generated At: ', $this->generatedAt],
            [],
            ['Date', 'Total Sales Revenue', 'Total Expense', 'Net Profit', 'Profit Margin (%)'],
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
                $sheet->getStyle('A8:E8')->getFont()->setBold(true);
                $sheet->getStyle('A8:E8')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFE9ECEF');

                // Add totals row
                $lastRow = count($this->data) + 9;
                $sheet->setCellValue("A{$lastRow}", 'Total');
                $sheet->getStyle("A{$lastRow}")->getFont()->setBold(true);
                $sheet->getStyle("A{$lastRow}")->getAlignment()->setHorizontal('right');
                
                $sheet->setCellValue("B{$lastRow}", $this->totalSalesRevenue);
                $sheet->setCellValue("C{$lastRow}", $this->totalExpenses);
                $sheet->setCellValue("D{$lastRow}", $this->totalNetProfit);
                $sheet->setCellValue("E{$lastRow}", $this->totalProfitMargin);
                $sheet->getStyle("B{$lastRow}:E{$lastRow}")->getFont()->setBold(true);
            }
        ];
    }
}

