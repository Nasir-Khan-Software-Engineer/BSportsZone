<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Net Profit Report PDF</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h2 class="text-center" style="margin: 0px;">{{ $companyName }}</h2>
    <h4 class="text-center" style="margin: 0px;">Net Profit Report</h4>

    <div style="margin-bottom: 10px;">
        <strong>POSID:</strong> {{ $posid }}
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
                <th class="text-right">Total Sales Revenue</th>
                <th class="text-right">Total Expense</th>
                <th class="text-right">Net Profit</th>
                <th class="text-right">Profit Margin (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($netProfitData as $item)
            <tr>
                <td class="text-center">{{ $item['formattedDate'] ?? '-' }}</td>
                <td class="text-right">{{ $item['totalSalesRevenue'] ?? '0' }}</td>
                <td class="text-right">{{ $item['totalExpenses'] ?? '0' }}</td>
                <td class="text-right">{{ $item['netProfit'] ?? '0' }}</td>
                <td class="text-right">{{ $item['profitMargin'] ?? '0%' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th class="text-right">Total</th>
                <th class="text-right">{{ $totals['totalSalesRevenue'] }}</th>
                <th class="text-right">{{ $totals['totalExpenses'] }}</th>
                <th class="text-right">{{ $totals['totalNetProfit'] }}</th>
                <th class="text-right">{{ $totals['totalProfitMargin'] }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>

