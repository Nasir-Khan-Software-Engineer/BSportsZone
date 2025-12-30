<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AccountSetup\IAccountSetupService;
use App\Models\Shop;
use App\Models\Accountinfo;
use App\Models\Sales;
use App\Models\SalesPayment;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Product;
use App\Models\Sales_items;
use Carbon\Carbon;
use Illuminate\Support\Number;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(IAccountSetupService $accountSetupService)
    {
        // $this->middleware('auth');
        $this->accountSetupService = $accountSetupService;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        return view('dashboard.index');
    }

    public function filterData(Request $request)
    {
        $filter = $request->input('filter');

        $posid = auth()->user()->posid;

        $salesPaymentQuery = SalesPayment::where('posid', $posid);
        $SalesQuery = Sales::where('posid', $posid);
        $expenseQuery = Expense::where('posid', $posid);
       
        $today = Carbon::today();
        
        switch ($filter) {
            case 'today':
                $salesPaymentQuery->whereDate('created_at', $today);
                $expenseQuery->whereDate('expenseDate', $today);
                $SalesQuery->whereDate('created_at', $today);
                break;

            case 'yesterday':
                $salesPaymentQuery->whereDate('created_at', $today->copy()->subDay());
                $expenseQuery->whereDate('expenseDate', $today->copy()->subDay());
                $SalesQuery->whereDate('created_at', $today->copy()->subDay());
                break;

            case 'thisWeek':
                // Week start Saturday, end Friday
                $start = $today->copy()->previous(Carbon::SATURDAY);
                if ($today->dayOfWeek === Carbon::SATURDAY) {
                    $start = $today->copy();
                }
                $end = $start->copy()->addDays(6)->endOfDay(); // include full Friday
                $salesPaymentQuery->whereBetween('created_at', [$start, $end]);
                $expenseQuery->whereBetween('expenseDate', [$start, $end]);
                $SalesQuery->whereBetween('created_at', [$start, $end]);
                break;

            case 'lastWeek':
                // Last week start (previous week's Saturday)
                $start = $today->copy()->previous(Carbon::SATURDAY)->subWeek();
                if ($today->dayOfWeek === Carbon::SATURDAY) {
                    $start = $today->copy()->subWeek();
                }
                $end = $start->copy()->addDays(6)->endOfDay();
                $salesPaymentQuery->whereBetween('created_at', [$start, $end]);
                $expenseQuery->whereBetween('expenseDate', [$start, $end]);
                $SalesQuery->whereBetween('created_at', [$start, $end]);
                break;

            case 'thisMonth':
                $start = $today->copy()->startOfMonth();
                $end = $today->copy()->endOfMonth()->endOfDay();
                $salesPaymentQuery->whereBetween('created_at', [$start, $end]);
                $expenseQuery->whereBetween('expenseDate', [$start, $end]);
                $SalesQuery->whereBetween('created_at', [$start, $end]);
                break;

            case 'lastMonth':
                $start = $today->copy()->startOfMonth()->subMonth();
                $end = $start->copy()->endOfMonth()->endOfDay();
                $salesPaymentQuery->whereBetween('created_at', [$start, $end]);
                $expenseQuery->whereBetween('expenseDate', [$start, $end]);
                $SalesQuery->whereBetween('created_at', [$start, $end]);
                break;

            default:
                // default to today if invalid filter
                $salesPaymentQuery->whereDate('created_at', $today);
                $expenseQuery->whereDate('expenseDate', $today);
                $SalesQuery->whereDate('created_at', $today);
                break;
        }

        // Sum total_payable_amount
        $totalSalesAmount = str_replace("BDT", "Tk", Number::currency($salesPaymentQuery->sum('paid_amount'), 'BDT'));
        $discountAmount = $SalesQuery->sum('discount_amount');
        $adjustmentAmount = $SalesQuery->sum('adjustmentAmt');
        $totalNumberOfSales = $SalesQuery->count();
        $totalCustomers = $SalesQuery->distinct('customerId')->count('customerId');

        // Sum amount
        $totalExpense = str_replace("BDT", "Tk", Number::currency($expenseQuery->sum('amount') ?? 0, 'BDT'));

        // Calculate customer distribution (New vs Returning)
        $customerDistribution = $this->calculateCustomerDistribution($posid, $filter);

        $totals = $salesPaymentQuery
        ->selectRaw("
            SUM(CASE WHEN payment_method = 'cash' THEN paid_amount ELSE 0 END) as total_cash,
            SUM(CASE WHEN payment_method = 'card' THEN paid_amount ELSE 0 END) as total_card,
            SUM(CASE WHEN payment_method = 'wallet' AND payment_via = 'bkash' THEN paid_amount ELSE 0 END) as total_bkash,
            SUM(CASE WHEN payment_method = 'wallet' AND payment_via = 'nagad' THEN paid_amount ELSE 0 END) as total_nagad,
            SUM(CASE WHEN payment_method = 'wallet' AND payment_via = 'rocket' THEN paid_amount ELSE 0 END) as total_rocket
        ")
        ->first();

        // Dummy split of totalSalesAmount for payment methods (just example ratios)
        $discountAmount = str_replace("BDT", "Tk", Number::currency($discountAmount ?? 0, 'BDT'));
        $adjustmentAmount = str_replace("BDT", "Tk", Number::currency($adjustmentAmount ?? 0, 'BDT'));


        return response()->json([
            'totalExpense' => $totalExpense,
            'totalCustomers' => $totalCustomers,
            'totalNumberOfSales' => $totalNumberOfSales,
            'totalSalesAmount' => $totalSalesAmount,
            'discountAmount' => $discountAmount,
            'adjustmentAmount' => $adjustmentAmount,
            'newCustomers' => $customerDistribution['new_customers'],
            'returningCustomers' => $customerDistribution['returning_customers'],
             'sales' => [
                'cash'   => $totals->total_cash ?? 0,
                'card'   => $totals->total_card ?? 0,
                'bkash'  => $totals->total_bkash ?? 0,
                'nagad'  => $totals->total_nagad ?? 0,
                'rocket' => $totals->total_rocket ?? 0,
            ],
        ]);
    }

    public function fixedMetrics(Request $request) {
        $posid = auth()->user()->posid;
        $topServices = $this->getTopServicesLast12Months($posid);
        $monthlySalesAndExpense = $this->monthlySalesExpenseLast12Months($posid);

        return response()->json([
            'topServices' => $topServices,
            'monthlySalesAndExpense' => $monthlySalesAndExpense
        ]);
    }


    /**
     * Set shop ID to the session and redirect to the home
     *
     * @param $id {int} new shop id
     * @return redirect to home
     */
    public function changeCurrentShop(Request $request, $id){

        $shopId = (int) $id;
        $isShopExist = Shop::where('posid', auth()->user()->posid)
                        ->where('id', $shopId)
                        ->exists();

        if($shopId > 0 && $isShopExist){
            $request->session()->put('shopid', $shopId);
        }

        return redirect()->route('dashboard.dashboard');
    }

    /**
     * Calculate customer distribution (New vs Returning) based on sales history
     *
     * @param int $posid
     * @param string $filter
     * @return array
     */
    private function calculateCustomerDistribution($posid, $filter)
    {
        $today = Carbon::today();
        
        // Apply the same date filter logic as the main dashboard
        $salesQuery = Sales::where('posid', $posid);
        
        switch ($filter) {
            case 'today':
                $salesQuery->whereDate('created_at', $today);
                break;
            case 'yesterday':
                $salesQuery->whereDate('created_at', $today->copy()->subDay());
                break;
            case 'thisWeek':
                $start = $today->copy()->previous(Carbon::SATURDAY);
                if ($today->dayOfWeek === Carbon::SATURDAY) {
                    $start = $today->copy();
                }
                $end = $start->copy()->addDays(6)->endOfDay();
                $salesQuery->whereBetween('created_at', [$start, $end]);
                break;
            case 'lastWeek':
                $start = $today->copy()->previous(Carbon::SATURDAY)->subWeek();
                if ($today->dayOfWeek === Carbon::SATURDAY) {
                    $start = $today->copy()->subWeek();
                }
                $end = $start->copy()->addDays(6)->endOfDay();
                $salesQuery->whereBetween('created_at', [$start, $end]);
                break;
            case 'thisMonth':
                $start = $today->copy()->startOfMonth();
                $end = $today->copy()->endOfMonth()->endOfDay();
                $salesQuery->whereBetween('created_at', [$start, $end]);
                break;
            case 'lastMonth':
                $start = $today->copy()->startOfMonth()->subMonth();
                $end = $start->copy()->endOfMonth()->endOfDay();
                $salesQuery->whereBetween('created_at', [$start, $end]);
                break;
            default:
                $salesQuery->whereDate('created_at', $today);
                break;
        }

        // Get all customers who made Sales in the filtered period
        $customersInPeriod = $salesQuery->distinct('customerId')->pluck('customerId')->toArray();
        
        if (empty($customersInPeriod)) {
            return [
                'new_customers' => 0,
                'returning_customers' => 0
            ];
        }

        // Count customers who made their first sales in this period (New Customers)
        $newCustomers = 0;
        $returningCustomers = 0;

        foreach ($customersInPeriod as $customerId) {
            // Check if this customer had any Sales before the current period
            $hasPreviousSales = Sales::where('posid', $posid)
                ->where('customerId', $customerId)
                ->where(function($query) use ($filter, $today) {
                    switch ($filter) {
                        case 'today':
                            $query->whereDate('created_at', '<', $today);
                            break;
                        case 'yesterday':
                            $query->whereDate('created_at', '<', $today->copy()->subDay());
                            break;
                        case 'thisWeek':
                            $start = $today->copy()->previous(Carbon::SATURDAY);
                            if ($today->dayOfWeek === Carbon::SATURDAY) {
                                $start = $today->copy();
                            }
                            $query->where('created_at', '<', $start);
                            break;
                        case 'lastWeek':
                            $start = $today->copy()->previous(Carbon::SATURDAY)->subWeek();
                            if ($today->dayOfWeek === Carbon::SATURDAY) {
                                $start = $today->copy()->subWeek();
                            }
                            $query->where('created_at', '<', $start);
                            break;
                        case 'thisMonth':
                            $query->where('created_at', '<', $today->copy()->startOfMonth());
                            break;
                        case 'lastMonth':
                            $start = $today->copy()->startOfMonth()->subMonth();
                            $query->where('created_at', '<', $start);
                            break;
                        default:
                            $query->whereDate('created_at', '<', $today);
                            break;
                    }
                })
                ->exists();

            if ($hasPreviousSales) {
                $returningCustomers++;
            } else {
                $newCustomers++;
            }
        }

        return [
            'new_customers' => $newCustomers,
            'returning_customers' => $returningCustomers
        ];
    }

    /**
     * Get Top 5 Services based on total taken count
     *
     * @param int $posid
     * @param string $filter
     * @return array
     */
    private function getTopServicesLast12Months($posid)
    {
        // Last 11 months + current month (total 12 months)
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();
        $endDate   = Carbon::now()->endOfMonth();

        $serviceQuery = Sales_items::join('sales', 'sales_items.sales_id', '=', 'sales.id')
            ->join('products', 'sales_items.product_id', '=', 'products.id')
            ->where('sales.posid', $posid)
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->selectRaw('products.name as service_name, SUM(sales_items.quantity) as total_count')
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_count', 'desc')
            ->limit(8);

        $results = $serviceQuery->get();

        return $results->map(function ($item) {
            return [
                'service_name' => $item->service_name,
                'total_count'  => (int) $item->total_count
            ];
        })->toArray();
    }


    private function monthlySalesExpenseLast12Months($posid)
    {
        // Current month + previous 11 months (total 12)
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();
        $endDate   = Carbon::now()->endOfMonth();

        // EXPENSE (filtered by posid)
        $expense = Expense::select(
                DB::raw('DATE_FORMAT(expenseDate, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->where('posid', $posid)
            ->whereBetween('expenseDate', [$startDate, $endDate])
            ->groupBy('month')
            ->pluck('total', 'month');

        // SALES (filtered by posid)
        $sales = Sales::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(total_amount) as total')
            )
            ->where('posid', $posid)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->pluck('total', 'month');

        // Generate exact 12 months (rolling)
        $months = [];
        $period = new \DatePeriod(
            $startDate,
            new \DateInterval('P1M'),
            Carbon::now()->addMonth()->startOfMonth()
        );

        foreach ($period as $date) {
            $key = $date->format('Y-m');

            $months['labels'][]  = $date->format('M Y');
            $months['expense'][] = $expense[$key] ?? 0;
            $months['sales'][]   = $sales[$key] ?? 0;
        }

        return response()->json($months);
    }


}