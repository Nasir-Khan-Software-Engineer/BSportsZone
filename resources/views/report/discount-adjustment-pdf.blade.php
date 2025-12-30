<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Discount & Adjustment Summary Report PDF</title>
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
    <h4 class="text-center" style="margin: 0px;">Discount & Adjustment Summary Report</h4>

    <div style="margin-bottom: 10px;">
        <strong>POSID:</strong> {{ $POSID }}
    </div>
    <div style="margin-bottom: 10px;">
        <strong>From:</strong> {{ $fromDate }} &nbsp;&nbsp; <strong>To:</strong> {{ $toDate }}
    </div>
    <div style="margin-bottom: 10px;">
        <strong>Generated At:</strong> {{$reportGenerationDateTime}}
    </div>
    
    <table>
        <thead>
            <tr>
                <th class="text-center">Date</th>
                <th class="text-right">Total Discount Amount</th>
                <th class="text-right">Total Positive Adjustment</th>
                <th class="text-right">Total Negative Adjustment</th>
                <th class="text-right">Net Adjustment Impact</th>
            </tr>
        </thead>
        <tbody>
            @foreach($summaryData as $item)
            <tr>
                <td class="text-center">{{ $item['formattedDate'] ?? '-' }}</td>
                <td class="text-right">{{ $item['totalDiscountAmount'] ?? '0' }}</td>
                <td class="text-right">{{ $item['totalPositiveAdjustment'] ?? '0' }}</td>
                <td class="text-right">{{ $item['totalNegativeAdjustment'] ?? '0' }}</td>
                <td class="text-right">{{ $item['netAdjustmentImpact'] ?? '0' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th class="text-right">Total</th>
                <th class="text-right">{{ $totals['totalDiscountAmount'] }}</th>
                <th class="text-right">{{ $totals['totalPositiveAdjustment'] }}</th>
                <th class="text-right">{{ $totals['totalNegativeAdjustment'] }}</th>
                <th class="text-right">{{ $totals['totalNetAdjustment'] }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>

