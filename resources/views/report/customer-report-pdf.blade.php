<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Report PDF</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 3px; text-align: left; }
        th { background: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .info-box { border: 1px solid #333; padding: 8px; margin-bottom: 10px; background: #f9f9f9; }
        .info-box h5 { margin: 0 0 5px 0; font-size: 11px; }
        .info-box p { margin: 2px 0; font-size: 10px; }
    </style>
</head>
<body>
    <h2 class="text-center" style="margin: 0px;">{{ $companyName }}</h2>
    <h4 class="text-center" style="margin: 0px;">Customer Report</h4>

    <div style="margin-bottom: 10px;">
        <strong>POSID:</strong> {{ $POSID }}
    </div>
    <div style="margin-bottom: 10px;">
        <strong>From:</strong> {{ $fromDate }} &nbsp;&nbsp; <strong>To:</strong> {{ $toDate }}
    </div>
    @if($customerType !== 'all')
    <div style="margin-bottom: 10px;">
        <strong>Customer Type:</strong> {{ $customerType }}
    </div>
    @endif
    <div style="margin-bottom: 10px;">
        <strong>Generated At:</strong> {{$reportGenerationDateTime}}
    </div>

    <!-- Customer Type Definitions -->
    <div class="info-box">
        <h5>Customer Type Definitions:</h5>
        <p><strong>New Customer:</strong> A customer who has taken exactly one service in their lifetime, and that service was taken within the last three months.</p>
        <p><strong>Regular Customer:</strong> A customer who has taken multiple services and has taken at least one service within the last three months.</p>
        <p><strong>Returning Customer:</strong> A customer who has taken multiple services in their lifetime.</p>
        <p><strong>Old Customer:</strong> A customer who has taken at least one service in their lifetime but has not taken any services within the last three months.</p>
        <p><strong>Inactive Customer:</strong> A customer who has not taken any services in their lifetime.</p>
        <p style="margin-top: 5px; font-size: 9px; color: #666;"><em>Note: Customer type is determined based on lifetime Sales, not filtered by date range.</em></p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th class="text-center">Customer ID</th>
                <th class="text-center">Customer Name</th>
                <th class="text-center">Phone</th>
                <th class="text-right">Total Sales</th>
                <th class="text-right">Total Quantity</th>
                <th class="text-right">Total Spending</th>
                <th class="text-right">Total Discount Amount</th>
                <th class="text-right">Total Adjustment Amount</th>
                <th class="text-center">Last Visited Date</th>
                <th class="text-center">Type</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customerData as $item)
            <tr>
                <td class="text-center">{{ $item['customer_id'] ?? '-' }}</td>
                <td class="text-center">
                    {{ isset($item['customer_name']) && strlen($item['customer_name']) > 20 
                        ? substr($item['customer_name'], 0, 20) . '...' 
                        : ($item['customer_name'] ?? '-') 
                    }}
                </td>
                <td class="text-center">{{ $item['phone'] ?? '-' }}</td>
                <td class="text-right">{{ $item['total_sales'] ?? 0 }}</td>
                <td class="text-right">{{ $item['total_quantity'] ?? 0 }}</td>
                <td class="text-right">{{ $item['total_spending'] ?? '0' }}</td>
                <td class="text-right">{{ $item['total_discount_amount'] ?? '0' }}</td>
                <td class="text-right">{{ $item['total_adjustment_amount'] ?? '0' }}</td>
                <td class="text-center">{{ $item['formatted_last_visited_date'] ?? '-' }}</td>
                <td class="text-center">{{ $item['customer_type'] ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

