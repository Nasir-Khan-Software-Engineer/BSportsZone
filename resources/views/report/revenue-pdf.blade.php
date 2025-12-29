<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Revenue Report PDF</title>
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
    <h4 class="text-center" style="margin: 0px;">Revenue Report</h4>

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
                <th class="text-center">Code</th>
                <th class="text-center">Service Name</th>
                <th class="text-right">Price</th>
                <th class="text-right">Quantity Sold</th>
                <th class="text-right">Revenue</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revenueData as $item)
            <tr>
                <td class="text-center">{{ $item['code'] ?? '-' }}</td>
                <td class="text-center">
                    {{ isset($item['name']) && strlen($item['name']) > 30 
                        ? substr($item['name'], 0, 30) . '...' 
                        : ($item['name'] ?? '-') 
                    }}
                </td>
                <td class="text-right">{{ $item['price'] ?? '0' }}</td>
                <td class="text-right">{{ $item['quantity_sold'] ?? 0 }}</td>
                <td class="text-right">{{ $item['revenue'] ?? '0' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Total</th>
                <th class="text-right">{{ $totals['totalQuantity'] ?? 0 }}</th>
                <th class="text-right">{{ $totals['totalRevenue'] }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>

