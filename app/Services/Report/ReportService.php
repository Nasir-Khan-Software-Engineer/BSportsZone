<?php
namespace App\Services\Report;

use App\Models\Purchases;
use App\Models\Expense;
use Carbon\Carbon;
use Number;

class ReportService implements IReportService{

    public function __construct(){
        // currently we are ignoring the repository
    }

    public function getSalesDetailsReportData($posId, $from, $to, $start, $length, $type= 'view'){
        
        $query = Purchases::where('posid', $posId);
        $totalRecord = Purchases::where('posid', $posId)->count();


        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $query->with(['customer', 'payments', 'createdByUser']);

        $totalFilteredRecord = $query->count();
        $totalAmount = str_replace('BDT', 'Tk.', Number::currency((clone $query)->sum('total_amount'), 'BDT'));;
        $totalPayable = str_replace('BDT', 'Tk.', Number::currency((clone $query)->sum('total_payable_amount'), 'BDT'));
        $totalDiscount = str_replace('BDT', 'Tk.', Number::currency((clone $query)->sum('discount_amount'), 'BDT'));
        $totalAdjustmentAmt = str_replace('BDT', 'Tk.', Number::currency((clone $query)->sum('adjustmentAmt'), 'BDT'));
        $totalPaid = str_replace('BDT', 'Tk.', Number::currency((clone $query)->get()->pluck('payments')->flatten()->sum('paid_amount'), 'BDT'));

        $sales = null;

        if($type === 'view'){
            // for view data table, we shows paginated data
            $sales = (clone $query)->orderBy('created_at', 'desc')->skip($start)->take($length)->get();
        }
        else{
            // for download report, we need all data
            $sales = (clone $query)->orderBy('created_at', 'desc')->get();
        }

        $sales->transform(function ($sale) {
            $sale->formattedDate = formatDate($sale->created_at);
            $sale->formattedTime = formatTime($sale->created_at);

            $sale->total_amount = str_replace('BDT', 'Tk.', Number::currency($sale->total_amount, 'BDT'));
            $sale->total_payable_amount = str_replace('BDT', 'Tk.', Number::currency($sale->total_payable_amount, 'BDT'));
            $sale->paidAmount = str_replace('BDT', 'Tk.', Number::currency($sale->payments->sum('paid_amount'), 'BDT'));
            $sale->discount_amount = str_replace('BDT', 'Tk.', Number::currency($sale->discount_amount, 'BDT'));
            $sale->adjustmentAmt = str_replace('BDT', 'Tk.', Number::currency($sale->adjustmentAmt, 'BDT'));
            
            return $sale;
        });

        return [
            'data' => $sales,
            'recordsTotal' => $totalRecord,
            'recordsFiltered' => $totalFilteredRecord,
            'totals' => [
                'totalAmount' => $totalAmount,
                'totalPayable' => $totalPayable,
                'totalDiscountAmount' => $totalDiscount,
                'totalPaid' => $totalPaid,
                'totalAdjustmentAmt' => $totalAdjustmentAmt
            ]
        ];
    }

    public function getExpenseDetailsReportData($posId, $from, $to, $start, $length, $type= 'view'){
        
        $query = Expense::where('posid', $posId);
        $totalRecord = Expense::where('posid', $posId)->count();

        if ($from) {
            $query->whereDate('expenseDate', '>=', $from);
        }
        if ($to) {
            $query->whereDate('expenseDate', '<=', $to);
        }

        $query->with(['expenseCategory', 'creator']);

        $totalFilteredRecord = $query->count();
        $totalAmount = str_replace('BDT', 'Tk.', Number::currency((clone $query)->sum('amount'), 'BDT'));

        $expenses = null;

        if($type === 'view'){
            // for view data table, we shows paginated data
            $expenses = (clone $query)->orderBy('id', 'desc')->skip($start)->take($length)->get();
        }
        else{
            // for download report, we need all data
            $expenses = (clone $query)->orderBy('id', 'desc')->get();
        }

        $expenses->transform(function ($expense) {
            $expense->formattedDate = formatDate($expense->expenseDate);
            $expense->formattedTime = formatTime($expense->expenseDate);
            $expense->formattedCreatedAt = formatDate($expense->created_at);
            $expense->formattedCreatedAtTime = formatTime($expense->created_at);
            $expense->amount = str_replace('BDT', 'Tk.', Number::currency($expense->amount, 'BDT'));
            
            return $expense;
        });

        return [
            'data' => $expenses,
            'recordsTotal' => $totalRecord,
            'recordsFiltered' => $totalFilteredRecord,
            'totals' => [
                'totalAmount' => $totalAmount
            ]
        ];
    }

    public function getDiscountAdjustmentReportData($posId, $from, $to, $start, $length, $type= 'view'){
        
        $query = Purchases::where('posid', $posId);

        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        // Get all purchases in the date range
        $purchases = $query->get();

        // Group by date and calculate totals
        $groupedData = $purchases->groupBy(function($purchase) {
            return $purchase->created_at->format('Y-m-d');
        })->map(function($dayPurchases, $date) {
            $totalDiscount = $dayPurchases->sum('discount_amount');
            $positiveAdjustment = $dayPurchases->where('adjustmentAmt', '>', 0)->sum('adjustmentAmt');
            $negativeAdjustment = abs($dayPurchases->where('adjustmentAmt', '<', 0)->sum('adjustmentAmt'));
            $netAdjustment = $positiveAdjustment - $negativeAdjustment;

            return [
                'date' => $date,
                'formattedDate' => formatDate($dayPurchases->first()->created_at),
                'totalDiscountAmount' => $totalDiscount,
                'totalPositiveAdjustment' => $positiveAdjustment,
                'totalNegativeAdjustment' => $negativeAdjustment,
                'netAdjustmentImpact' => $netAdjustment,
            ];
        })->values();

        // Sort by date descending
        $groupedData = $groupedData->sortByDesc('date')->values();

        $totalRecord = $groupedData->count();
        $totalFilteredRecord = $totalRecord;

        // Format the amounts
        $groupedData->transform(function ($item) {
            $item['totalDiscountAmount'] = str_replace('BDT', 'Tk.', Number::currency($item['totalDiscountAmount'], 'BDT'));
            $item['totalPositiveAdjustment'] = str_replace('BDT', 'Tk.', Number::currency($item['totalPositiveAdjustment'], 'BDT'));
            $item['totalNegativeAdjustment'] = str_replace('BDT', 'Tk.', Number::currency($item['totalNegativeAdjustment'], 'BDT'));
            $item['netAdjustmentImpact'] = str_replace('BDT', 'Tk.', Number::currency($item['netAdjustmentImpact'], 'BDT'));
            return $item;
        });

        // Calculate totals
        $totalDiscount = str_replace('BDT', 'Tk.', Number::currency($purchases->sum('discount_amount'), 'BDT'));
        $totalPositiveAdjustment = str_replace('BDT', 'Tk.', Number::currency($purchases->where('adjustmentAmt', '>', 0)->sum('adjustmentAmt'), 'BDT'));
        $totalNegativeAdjustment = str_replace('BDT', 'Tk.', Number::currency(abs($purchases->where('adjustmentAmt', '<', 0)->sum('adjustmentAmt')), 'BDT'));
        $totalNetAdjustment = str_replace('BDT', 'Tk.', Number::currency($purchases->sum('adjustmentAmt'), 'BDT'));

        $summaryData = null;

        if($type === 'view'){
            // for view data table, we shows paginated data
            $summaryData = $groupedData->slice($start, $length)->values();
        }
        else{
            // for download report, we need all data
            $summaryData = $groupedData;
        }

        return [
            'data' => $summaryData,
            'recordsTotal' => $totalRecord,
            'recordsFiltered' => $totalFilteredRecord,
            'totals' => [
                'totalDiscountAmount' => $totalDiscount,
                'totalPositiveAdjustment' => $totalPositiveAdjustment,
                'totalNegativeAdjustment' => $totalNegativeAdjustment,
                'totalNetAdjustment' => $totalNetAdjustment
            ]
        ];
    }

    public function getRevenueReportData($posId, $from, $to, $start, $length, $type= 'view'){
        
        $query = Purchases::where('posid', $posId);

        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $query->with(['items.product', 'payments']);

        // Get all purchases in the date range
        $purchases = $query->get();

        // Calculate total revenue from paid amounts
        $totalRevenue = $purchases->pluck('payments')->flatten()->sum('paid_amount');
        
        // Calculate number of sales
        $numberOfSales = $purchases->count();
        
        // Calculate average sale value
        $averageSaleValue = $numberOfSales > 0 ? $totalRevenue / $numberOfSales : 0;

        // Group purchase items by product to calculate service/product revenue
        // Revenue per item = selling_price * quantity
        $productRevenueData = [];
        
        foreach ($purchases as $purchase) {
            foreach ($purchase->items as $item) {
                $productId = $item->product_id;
                
                // Calculate revenue for this item: selling_price * quantity
                $itemRevenue = ($item->selling_price ?? 0) * ($item->quantity ?? 0);
                
                if (!isset($productRevenueData[$productId])) {
                    $productRevenueData[$productId] = [
                        'product_id' => $productId,
                        'code' => $item->product->code ?? '',
                        'name' => $item->product->name ?? '',
                        'price' => $item->selling_price ?? 0,
                        'quantity_sold' => 0,
                        'revenue' => 0,
                    ];
                }
                
                $productRevenueData[$productId]['quantity_sold'] += $item->quantity;
                $productRevenueData[$productId]['revenue'] += $itemRevenue;
            }
        }

        // Calculate totals before formatting
        $totalQuantity = array_sum(array_column($productRevenueData, 'quantity_sold'));

        // Convert to collection and format
        $revenueData = collect($productRevenueData)->map(function($item) {
            $item['revenue'] = str_replace('BDT', 'Tk.', Number::currency($item['revenue'], 'BDT'));
            $item['price'] = str_replace('BDT', 'Tk.', Number::currency($item['price'], 'BDT'));
            return $item;
        })->values();

        // Sort by revenue descending
        $revenueData = $revenueData->sortByDesc(function($item) {
            // Extract numeric value for sorting
            return floatval(str_replace(['Tk.', ',', ' '], '', $item['revenue']));
        })->values();

        $totalRecord = $revenueData->count();
        $totalFilteredRecord = $totalRecord;

        // Calculate service revenue (for now, all items are treated as services)
        $serviceRevenue = $totalRevenue; // All revenue is service revenue in this system
        $productRevenue = 0; // Can be separated later if needed

        $summaryData = null;

        if($type === 'view'){
            // for view data table, we shows paginated data
            $summaryData = $revenueData->slice($start, $length)->values();
        }
        else{
            // for download report, we need all data
            $summaryData = $revenueData;
        }

        return [
            'data' => $summaryData,
            'recordsTotal' => $totalRecord,
            'recordsFiltered' => $totalFilteredRecord,
            'summary' => [
                'totalRevenue' => str_replace('BDT', 'Tk.', Number::currency($totalRevenue, 'BDT')),
                'serviceRevenue' => str_replace('BDT', 'Tk.', Number::currency($serviceRevenue, 'BDT')),
                'productRevenue' => str_replace('BDT', 'Tk.', Number::currency($productRevenue, 'BDT')),
                'averageSaleValue' => str_replace('BDT', 'Tk.', Number::currency($averageSaleValue, 'BDT')),
            ],
            'totals' => [
                'totalQuantity' => $totalQuantity,
                'totalRevenue' => str_replace('BDT', 'Tk.', Number::currency($totalRevenue, 'BDT')),
            ]
        ];
    }

    public function getNetProfitReportData($posId, $from, $to, $start = 0, $length = 9, $type = 'view'){
        
        // Get purchases with payments grouped by date
        $purchasesQuery = Purchases::where('posid', $posId);
        
        if ($from) {
            $purchasesQuery->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $purchasesQuery->whereDate('created_at', '<=', $to);
        }

        $purchases = $purchasesQuery->with('payments')->get();

        // Get expenses grouped by date
        $expensesQuery = Expense::where('posid', $posId);
        
        if ($from) {
            $expensesQuery->whereDate('expenseDate', '>=', $from);
        }
        if ($to) {
            $expensesQuery->whereDate('expenseDate', '<=', $to);
        }

        $expenses = $expensesQuery->get();

        // Group purchases by date
        $purchasesByDate = $purchases->groupBy(function($purchase) {
            $date = $purchase->created_at;
            // Ensure we have a Carbon instance
            if (!($date instanceof Carbon)) {
                $date = Carbon::parse($date);
            }
            return $date->format('Y-m-d');
        });

        // Group expenses by date
        $expensesByDate = $expenses->groupBy(function($expense) {
            $date = $expense->expenseDate;
            // Ensure we have a Carbon instance
            if (!($date instanceof Carbon)) {
                $date = Carbon::parse($date);
            }
            return $date->format('Y-m-d');
        });

        // Get all unique dates
        $allDates = $purchasesByDate->keys()->merge($expensesByDate->keys())->unique()->sort()->values();

        // Build daily data
        $dailyData = $allDates->map(function($date) use ($purchasesByDate, $expensesByDate) {
            $dayPurchases = $purchasesByDate->get($date, collect());
            $dayExpenses = $expensesByDate->get($date, collect());

            $dayRevenue = $dayPurchases->pluck('payments')->flatten()->sum('paid_amount');
            $dayExpense = $dayExpenses->sum('amount');
            $dayNetProfit = $dayRevenue - $dayExpense;
            $dayProfitMargin = $dayRevenue > 0 ? ($dayNetProfit / $dayRevenue) * 100 : 0;

            // Format date
            $dateObj = null;
            if ($dayPurchases->isNotEmpty()) {
                $dateObj = $dayPurchases->first()->created_at;
            } elseif ($dayExpenses->isNotEmpty()) {
                $dateObj = Carbon::parse($dayExpenses->first()->expenseDate);
            } else {
                $dateObj = Carbon::parse($date);
            }

            return [
                'date' => $date,
                'formattedDate' => formatDate($dateObj),
                'totalSalesRevenue' => $dayRevenue,
                'totalExpenses' => $dayExpense,
                'netProfit' => $dayNetProfit,
                'profitMargin' => $dayProfitMargin,
            ];
        })->values();

        // Calculate totals
        $totalSalesRevenue = $dailyData->sum('totalSalesRevenue');
        $totalExpenses = $dailyData->sum('totalExpenses');
        $totalNetProfit = $dailyData->sum('netProfit');
        $totalProfitMargin = $totalSalesRevenue > 0 ? ($totalNetProfit / $totalSalesRevenue) * 100 : 0;

        // Format amounts
        $dailyData->transform(function($item) {
            $item['totalSalesRevenue'] = str_replace('BDT', 'Tk.', Number::currency($item['totalSalesRevenue'], 'BDT'));
            $item['totalExpenses'] = str_replace('BDT', 'Tk.', Number::currency($item['totalExpenses'], 'BDT'));
            $item['netProfit'] = str_replace('BDT', 'Tk.', Number::currency($item['netProfit'], 'BDT'));
            $item['profitMargin'] = number_format($item['profitMargin'], 2) . '%';
            return $item;
        });

        // Sort by date descending
        $dailyData = $dailyData->sortByDesc('date')->values();

        $totalRecord = $dailyData->count();
        $totalFilteredRecord = $totalRecord;

        $summaryData = null;

        if($type === 'view'){
            // for view data table, we shows paginated data
            $summaryData = $dailyData->slice($start, $length)->values();
        }
        else{
            // for download report, we need all data
            $summaryData = $dailyData;
        }

        return [
            'data' => $summaryData,
            'recordsTotal' => $totalRecord,
            'recordsFiltered' => $totalFilteredRecord,
            'totals' => [
                'totalSalesRevenue' => str_replace('BDT', 'Tk.', Number::currency($totalSalesRevenue, 'BDT')),
                'totalExpenses' => str_replace('BDT', 'Tk.', Number::currency($totalExpenses, 'BDT')),
                'totalNetProfit' => str_replace('BDT', 'Tk.', Number::currency($totalNetProfit, 'BDT')),
                'totalProfitMargin' => number_format($totalProfitMargin, 2) . '%',
            ]
        ];
    }

    public function getCustomerReportData($posId, $from, $to, $customerType, $start, $length, $type = 'view'){
        
        // Get customers filtered by creation date
        $customersQuery = \App\Models\Customer::where('posid', $posId);
        
        if ($from) {
            $customersQuery->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $customersQuery->whereDate('created_at', '<=', $to);
        }

        $customers = $customersQuery->get();

        // Calculate three months ago date (from today, not from date range)
        $threeMonthsAgo = Carbon::now()->subMonths(3)->startOfDay();

        // Process each customer
        $customerData = $customers->map(function($customer) use ($threeMonthsAgo) {
            // Get ALL purchases for this customer (lifetime, not filtered by date range)
            $allPurchases = \App\Models\Purchases::where('customerId', $customer->id)
                ->where('posid', $customer->posid)
                ->with(['items', 'payments'])
                ->get();
            
            // Get purchases in last 3 months (from today, not from date range)
            $recentPurchases = $allPurchases->filter(function($purchase) use ($threeMonthsAgo) {
                if (!$purchase->created_at) {
                    return false;
                }
                $purchaseDate = $purchase->created_at instanceof Carbon ? $purchase->created_at : Carbon::parse($purchase->created_at);
                return $purchaseDate >= $threeMonthsAgo;
            });

            // Calculate metrics (from all purchases)
            $totalSales = $allPurchases->count();
            $totalQuantity = $allPurchases->pluck('items')->flatten()->sum('quantity');
            $totalSpending = $allPurchases->pluck('payments')->flatten()->sum('paid_amount');
            $totalDiscountAmount = $allPurchases->sum('discount_amount');
            $totalAdjustmentAmount = $allPurchases->sum('adjustmentAmt');
            
            // Get last visited date
            $lastPurchase = $allPurchases->sortByDesc('created_at')->first();
            $lastVisitedDate = $lastPurchase ? $lastPurchase->created_at : null;

            // Determine customer type based on updated definitions
            // New Customer: Exactly one service in lifetime, and that service was within the last 3 months
            // Regular Customer: Multiple services in lifetime, and at least one service within the last 3 months
            // Returning Customer: Multiple services in lifetime (regardless of when)
            // Old Customer: At least one service in lifetime, but none within the last 3 months
            // Inactive Customer: No services ever in lifetime
            
            $totalSales = $allPurchases->count();
            $recentSalesCount = $recentPurchases->count();
            
            if ($totalSales == 0) {
                $type = 'Inactive Customer';
            } elseif ($recentSalesCount == 0) {
                // No recent purchases
                if ($totalSales == 1) {
                    $type = 'Old Customer';
                } else {
                    // Multiple services but none recent
                    $type = 'Returning Customer';
                }
            } elseif ($recentSalesCount == 1 && $totalSales == 1) {
                // Exactly one service total and it's within last 3 months
                $type = 'New Customer';
            } elseif ($totalSales > 1 && $recentSalesCount > 0) {
                // Multiple services and at least one recent
                $type = 'Regular Customer';
            } else {
                // Fallback (shouldn't happen, but just in case)
                $type = 'Inactive Customer';
            }

            return [
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'phone' => $customer->phone1 ?? '',
                'total_sales' => $totalSales,
                'total_quantity' => $totalQuantity,
                'total_spending' => $totalSpending,
                'total_discount_amount' => $totalDiscountAmount,
                'total_adjustment_amount' => $totalAdjustmentAmount,
                'last_visited_date' => $lastVisitedDate,
                'formatted_last_visited_date' => $lastVisitedDate ? formatDate($lastVisitedDate) : '-',
                'customer_type' => $type,
            ];
        });

        // Filter by customer type
        if ($customerType !== 'all') {
            $customerData = $customerData->filter(function($item) use ($customerType) {
                return $item['customer_type'] === $customerType;
            });
        }

        $totalRecord = $customerData->count();
        $totalFilteredRecord = $totalRecord;

        // Format amounts
        $customerData->transform(function($item) {
            $item['total_spending'] = str_replace('BDT', 'Tk.', Number::currency($item['total_spending'], 'BDT'));
            $item['total_discount_amount'] = str_replace('BDT', 'Tk.', Number::currency($item['total_discount_amount'], 'BDT'));
            $item['total_adjustment_amount'] = str_replace('BDT', 'Tk.', Number::currency($item['total_adjustment_amount'], 'BDT'));
            return $item;
        });

        // Sort by total spending descending
        $customerData = $customerData->sortByDesc(function($item) {
            return floatval(str_replace(['Tk.', ',', ' '], '', $item['total_spending']));
        })->values();

        $summaryData = null;

        if($type === 'view'){
            // for view data table, we shows paginated data
            $summaryData = $customerData->slice($start, $length)->values();
        }
        else{
            // for download report, we need all data
            $summaryData = $customerData;
        }

        return [
            'data' => $summaryData,
            'recordsTotal' => $totalRecord,
            'recordsFiltered' => $totalFilteredRecord,
        ];
    }
}