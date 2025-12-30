<?php
namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExpenseDetailsReport implements FromArray, WithHeadings, WithEvents
{
    public function __construct($data)
    {
        $this->data = $data['data'];
        $this->companyName = $data['companyName'];
        $this->rptName = "Expense Report";
        $this->from = $data['fromDate'];
        $this->to = $data['toDate'];
        $this->generatedAt = $data['reportGenerationDateTime'];
        $this->POSID = $data['POSID'];

        $this->totalAmount = $data['totals']['totalAmount'];
    }

    public function array(): array
    {
        $result = $this->data->map(function ($item) {
            return [
                $item->id ?? '',
                $item->formattedDate ?? '',
                $item->title ?? '',
                $item->expenseCategory->title ?? 'N/A',
                $item->creator->name ?? 'N/A',
                $item->formattedCreatedAtTime . ' ' . $item->formattedCreatedAt ?? '',
                $item->amount ?? ''
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
            ['ID', 'Date', 'Title', 'Category', 'Created By', 'Created At', 'Amount'],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->mergeCells('A1:G1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                $sheet->mergeCells('A2:G2');
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

                $lastRow = count($this->data) + 8;
                $sheet->mergeCells("A{$lastRow}:F{$lastRow}");
                $sheet->setCellValue("A{$lastRow}", 'Total');
                $sheet->getStyle("A{$lastRow}")->getFont()->setBold(true);
                $sheet->getStyle("A{$lastRow}")->getAlignment()->setHorizontal('right');
                
                $sheet->setCellValue("G{$lastRow}", $this->totalAmount);
            }
        ];
    }
}
