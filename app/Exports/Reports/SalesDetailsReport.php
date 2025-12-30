<?php
namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesDetailsReport implements FromArray, WithHeadings, WithEvents
{
    public function __construct($data)
    {
        $this->data = $data['data'];
        $this->companyName = $data['companyName'];
        $this->rptName = "Sales Report";
        $this->from = $data['fromDate'];
        $this->to = $data['toDate'];
        $this->generatedAt = date('Y-m-d H:i:s');
        $this->POSID = $data['POSID'];

        $this->totalAmount = $data['totals']['totalAmount'];
        $this->totalDiscount = $data['totals']['totalDiscountAmount'];
        $this->totalAdjustmentAmt = $data['totals']['totalAdjustmentAmt'];
        $this->totalPayable = $data['totals']['totalPayable'];
        $this->totalPaid = $data['totals']['totalPaid'];
    }

    public function array(): array
    {
        $result = $this->data->map(function ($item) {
            return [
                $item->invoice_code ?? '',
                $item['customer']['name'] ?? 'Walk-in Customer',
                $item['customer']['phone1'] ?? '',
                $item->formattedTime.' '.$item->formattedDate ?? '',
                $item->total_amount ?? '',
                $item->discount_amount .' ('. ($item->discountType == 'fixed' ? $item->discount_value.' Fixed)' : $item->discount_value.'%)'),
                $item['adjustmentAmt'] ?? '',
                $item['total_payable_amount'] ?? '',
                $item['paidAmount'] ?? '',
                $item->createdByUser->name ?? ''
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
            ['Invoice ID', 'Customer Name', 'Customer Phone', 'Date', 'Total Amount', 'Discount Amount', 'Adjustment Amount', 'Payable Amount', 'Paid Amount', 'Sales By'],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->mergeCells('A1:I1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                $sheet->mergeCells('A2:I2');
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

                $lastRow = count($this->data) + 8;
                $sheet->mergeCells("A{$lastRow}:D{$lastRow}");
                $sheet->setCellValue("A{$lastRow}", 'Total');
                $sheet->getStyle("A{$lastRow}")->getFont()->setBold(true);
                $sheet->getStyle("A{$lastRow}")->getAlignment()->setHorizontal('right');
                
                $sheet->setCellValue("E{$lastRow}", $this->totalAmount);
                $sheet->setCellValue("F{$lastRow}", $this->totalDiscount);
                $sheet->setCellValue("G{$lastRow}", $this->totalAdjustmentAmt);
                $sheet->setCellValue("H{$lastRow}", $this->totalPayable);
                $sheet->setCellValue("I{$lastRow}", $this->totalPaid);
            }
        ];
    }
}