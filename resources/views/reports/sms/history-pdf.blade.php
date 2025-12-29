<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SMS History Report PDF</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 3px; text-align: left; }
        th { background: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        tfoot th { background: #e9ecef; font-weight: bold; }
    </style>
</head>
<body>
    <h2 class="text-center" style="margin: 0px;">{{ $companyName }}</h2>
    <h4 class="text-center" style="margin: 0px;">SMS History Report</h4>

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
                <th class="text-center">Date Time</th>
                <th class="text-center">Source</th>
                <th class="text-center">From Number</th>
                <th class="text-center">To Number</th>
                <th class="text-right">SMS Length</th>
                <th class="text-right">SMS Count</th>
                <th class="text-right">Unit Cost</th>
                <th class="text-right">Total Cost</th>
            </tr>
        </thead>
        <tbody>
            @foreach($smsData as $item)
            <tr>
                <td class="text-center">{{ $item['date_time'] ?? '-' }}</td>
                <td class="text-left">{{ $item['source'] ?? '-' }}</td>
                <td class="text-center">{{ $item['from_number'] ?? '-' }}</td>
                <td class="text-center">{{ $item['to_number'] ?? '-' }}</td>
                <td class="text-right">{{ $item['message_length'] ?? 0 }}</td>
                <td class="text-right">{{ $item['sms_count'] ?? 0 }}</td>
                <td class="text-right">{{ $item['unit_cost'] ?? '45 poysa' }}</td>
                <td class="text-right">{{ $item['total_cost'] ?? '0.00 taka' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">Total</th>
                <th class="text-right">{{ $totalMessageLength ?? 0 }}</th>
                <th class="text-right">{{ $totalSmsCount ?? 0 }}</th>
                <th></th>
                <th class="text-right">{{ $totalCost ?? '0.00 taka' }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>

