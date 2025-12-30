<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Expense Report PDF</title>
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
    <h4 class="text-center" style="margin: 0px;">Expense Report</h4>

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
                <th class="text-center">ID</th>
                <th class="text-center" style="width: 70px;">Date</th>
                <th class="text-center">Title</th>
                <th class="text-center">Category</th>
                <th class="text-center">Created By</th>
                <th class="text-center">Created At</th>
                <th class="text-center">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $expense)
            <tr>
                <td class="text-center">{{ $expense->id ?? '-' }}</td>
                <td class="text-center">{{ $expense->formattedDate ?? '-' }}</td>
                <td class="text-center">
                    {{ isset($expense->title) && strlen($expense->title) > 20 
                        ? substr($expense->title, 0, 20) . '...' 
                        : ($expense->title ?? '-') 
                    }}
                </td>
                <td class="text-center">{{ $expense->expenseCategory->title ?? '-' }}</td>
                <td class="text-center">
                    {{ isset($expense->creator->name) && strlen($expense->creator->name) > 13 
                        ? substr($expense->creator->name, 0, 13) . '...' 
                        : ($expense->creator->name ?? '-') 
                    }}
                </td>
                <td class="text-center">{{ $expense->formattedCreatedAtTime ?? '-' }} {{ $expense->formattedCreatedAt ?? '-' }}</td>
                <td class="text-center">{{ $expense->amount }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="text-right">Total</th>
                <th class="text-center">{{ $totals['totalAmount'] }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
