<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Employee Report PDF</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 3px; text-align: left; }
        th { background: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .info-box { border: 1px solid #333; padding: 8px; margin-bottom: 10px; background: #f9f9f9; }
        .info-box h5 { margin: 0 0 5px 0; font-size: 11px; }
        .info-box p { margin: 2px 0; font-size: 10px; }
    </style>
</head>
<body>
    <h2 class="text-center" style="margin: 0px;">{{ $companyName }}</h2>
    <h4 class="text-center" style="margin: 0px;">Employee Report</h4>

    <div style="margin-bottom: 10px;">
        <strong>POSID:</strong> {{ $POSID }}
    </div>
    <div style="margin-bottom: 10px;">
        <strong>From:</strong> {{ $fromDate }} &nbsp;&nbsp; <strong>To:</strong> {{ $toDate }}
    </div>
    @if($designationName !== 'All')
    <div style="margin-bottom: 10px;">
        <strong>Designation:</strong> {{ $designationName }}
    </div>
    @endif
    <div style="margin-bottom: 10px;">
        <strong>Generated At:</strong> {{$reportGenerationDateTime}}
    </div>

    <!-- Working Days Calculation Note -->
    <div class="info-box">
        <h5>Working Days Calculation:</h5>
        <p>Only days with attendance activity (Present, Absent, or Leave) are counted as working days. Days with no attendance records are considered holidays and excluded from the calculation.</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th class="text-center">Employee Name</th>
                <th class="text-center">Designation</th>
                <th class="text-center">Phone</th>
                <th class="text-right">Total Working Days</th>
                <th class="text-right">Present</th>
                <th class="text-right">Absent</th>
                <th class="text-right">Total Leave</th>
                <th class="text-right">Total Review</th>
                <th class="text-right">Positive Review</th>
                <th class="text-right">Warning Review</th>
                <th class="text-right">Negative Review</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employeeData as $item)
            <tr>
                <td class="text-left">
                    {{ isset($item['employee_name']) && strlen($item['employee_name']) > 20 
                        ? substr($item['employee_name'], 0, 20) . '...' 
                        : ($item['employee_name'] ?? '-') 
                    }}
                </td>
                <td class="text-left">{{ $item['designation'] ?? '-' }}</td>
                <td class="text-center">{{ $item['phone'] ?? '-' }}</td>
                <td class="text-right">{{ $item['total_working_days'] ?? 0 }}</td>
                <td class="text-right">{{ $item['present_display'] ?? '0% (0)' }}</td>
                <td class="text-right">{{ $item['absent_display'] ?? '0% (0)' }}</td>
                <td class="text-right">{{ $item['total_leave'] ?? 0 }}</td>
                <td class="text-right">{{ $item['total_review'] ?? 0 }}</td>
                <td class="text-right">{{ $item['positive_display'] ?? '0% (0)' }}</td>
                <td class="text-right">{{ $item['warning_display'] ?? '0% (0)' }}</td>
                <td class="text-right">{{ $item['negative_display'] ?? '0% (0)' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

