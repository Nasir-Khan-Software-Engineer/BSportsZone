<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Report PDF</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 3px; text-align: left; }
        th { background: #f2f2f2; }
        tfoot th { background: #e9ecef; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h2 class="text-center" style="margin: 0px;">{{ $companyName }}</h2>
    <h4 class="text-center" style="margin: 0px;">Sales Report</h4>

    <div style="margin-bottom: 10px;">
        <strong>POSID:</strong> {{ $POSID }}
    </div>
    <div style="margin-bottom: 10px;">
        <strong>From:</strong> {{ $fromDate }} &nbsp;&nbsp; <strong>To:</strong> {{ $toDate }}
    </div>
    <div style="margin-bottom: 10px;">
        <strong>Generated At:</strong> {{ \Carbon\Carbon::now()->format('Y-m-d h:i:s') }}
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Invoice No.</th>
                <th>Customer</th>
                <th class="text-right" style="width: 70px">Phone</th>
                <th class="text-center" style="width: 70px;">Date</th>
                <th class="text-right">Total Amt</th>
                <th class="text-right">Discount Amt</th>
                <th class="text-right">Adjustment Amt</th>
                <th class="text-right">Payable Amt</th>
                <th class="text-right">Paid Amt</th>
                <th>Sales By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
            <tr>
                <td>{{ $sale->invoice_code ?? '-' }}</td>
                <td>
                    {{ isset($sale->customer->name) && strlen($sale->customer->name) > 13 
                        ? substr($sale->customer->name, 0, 13) . '...' 
                        : ($sale->customer->name ?? '-') 
                    }}
                </td>
                <td class="text-right">{{ $sale->customer->phone1 ?? '-' }}</td>
                <td class="text-center">{{ $sale->formattedTime ?? '-' }} <br> {{$sale->formattedDate??'-'}}</td>
                <td class="text-right">{{ $sale->total_amount }}</td>
                <td class="text-right">{{ $sale->discount_amount }}</td>
                <td class="text-right">{{ $sale->adjustmentAmt }}</td>
                <td class="text-right">{{ $sale->total_payable_amount }}</td>
                <td class="text-right">{{ $sale->paidAmount }}</td>
                <td>
                    {{ isset($sale->createdByUser->name) && strlen($sale->createdByUser->name) > 13 
                        ? substr($sale->createdByUser->name, 0, 13) . '...' 
                        : ($sale->createdByUser->name ?? '-') 
                    }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">Total</th>
                <th class="text-right">{{ $totals['totalAmount'] }}</th>
                <th class="text-right">{{ $totals['totalDiscountAmount'] }}</th>
                <th class="text-right">{{ $totals['totalAdjustmentAmt'] }}</th>
                <th class="text-right">{{ $totals['totalPayable'] }}</th>
                <th class="text-right">{{ $totals['totalPaid'] }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
