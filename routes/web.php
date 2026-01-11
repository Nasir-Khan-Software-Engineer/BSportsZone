<?php

use App\Http\Controllers\Setup\BrandController;
use App\Http\Controllers\Setup\SupplierController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Setup\AccountSetupController;
use App\Http\Controllers\Setup\ShopSetupController;
use App\Http\Controllers\Setup\RoleSetupController;
use App\Http\Controllers\Setup\UserSetupController;
use App\Http\Controllers\Setup\CategoryController;
use App\Http\Controllers\Setup\CustomerController;
use App\Http\Controllers\Setup\UnitController;
use App\Http\Controllers\Utilities\ExpenseController;
use App\Http\Controllers\Utilities\ExpenseCategoryController;
use App\Http\Controllers\Service\ServiceController;
use App\Http\Controllers\Pos\PosController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Reports\SalesReportController;
use App\Http\Controllers\Reports\ExpenseReportController;
use App\Http\Controllers\Reports\DiscountAdjustmentReportController;
use App\Http\Controllers\Reports\RevenueReportController;
use App\Http\Controllers\Reports\NetProfitReportController;
use App\Http\Controllers\Reports\CustomerReportController;
use App\Http\Controllers\Report\EmployeeReportController;
use App\Http\Controllers\Report\StaffReportController;
use App\Http\Controllers\Report\SmsHistoryReportController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LoyaltyCardController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Employee\EmployeeReviewController;
use App\Http\Controllers\Attendance\AttendanceController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\VariationController;
use App\Http\Controllers\Stock\PurchaseController;

// help portal controllers
use App\Http\Controllers\HelpPortal\LoyaltyHelpController;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });


Route::middleware(['auth', 'checkSessionAndUserType'])->group(function(){

    Route::prefix('utilities')->as('utilities.')->group(function () {
        Route::prefix('expense/category')->controller(ExpenseCategoryController::class)->group(function () {
            Route::get('/', 'index')->name('expense.category.index')->middleware('permission');
            Route::get('/create', 'create')->name('expense.category.create');
            Route::post('/store', 'store')->name('expense.category.store')->middleware('permission');
            Route::get('/{category}/edit', 'edit')->name('expense.category.edit');
            Route::put('/{category}', 'update')->name('expense.category.update')->middleware('permission');
            Route::delete('/{category}', 'destroy')->name('expense.category.destroy')->middleware('permission');
        });

        Route::prefix('expenses')->controller(ExpenseController::class)->group(function () {
            Route::get('/', 'index')->name('expenses.index')->middleware('permission');
            Route::get('/datatable', 'datatable')->name('expenses.datatable');
            Route::get('/create', 'create')->name('expenses.create');
            Route::post('/store', 'store')->name('expenses.store')->middleware('permission');
            Route::get('/{expense}/edit', 'edit')->name('expenses.edit');
            Route::put('/{expense}', 'update')->name('expenses.update')->middleware('permission');
            Route::delete('/{expense}', 'destroy')->name('expenses.destroy')->middleware('permission');
        });
    });

    Route::prefix('setup')->as('setup.')->group(function () {
        Route::prefix('account')->controller(AccountSetupController::class)->group(function () {
            Route::get('/', 'index')->name('account.index')->middleware('permission');
            Route::post('/update', 'update')->name('account.update')->middleware('permission');
        });

        Route::prefix('posinfo')->controller(AccountSetupController::class)->group(function () {
            Route::post('/update', 'updatePosInformation')->name('posinfo.update')->middleware('permission');
        });

        Route::prefix('loyalty')->controller(AccountSetupController::class)->group(function () {
            Route::post('/update', 'updateLoyaltySettings')->name('loyalty.update');
        });

        Route::prefix('sms-template')->controller(AccountSetupController::class)->group(function () {
            Route::post('/update', 'updateSmsTemplate')->name('sms-template.update');
        });

        Route::prefix('sms-config')->controller(AccountSetupController::class)->group(function () {
            Route::post('/update', 'updateSmsConfig')->name('sms-config.update');
        });

        Route::prefix('user')->controller(UserSetupController::class)->group(function () {
            Route::get('/', 'index')->name('user.index')->middleware('permission');
            Route::get('/create', 'create')->name('user.create');
            Route::post('/store', 'store')->name('user.store')->middleware('permission');
            Route::get('/{user}', 'show')->name('user.show')->middleware('permission');
            Route::get('/{user}/edit', 'edit')->name('user.edit');
            Route::post('/{user}', 'update')->name('user.update')->middleware('permission');
            Route::delete('/{user}', 'destroy')->name('user.destroy')->middleware('permission');
        });

        Route::prefix('role')->controller(RoleSetupController::class)->group(function () {
            Route::get('/', 'index')->name('role.index')->middleware('permission');
            Route::get('/create', 'create')->name('role.create');
            Route::post('/store', 'store')->name('role.store')->middleware('permission');
            Route::get('/{role}/show', 'show')->name('role.show')->middleware('permission');
            Route::get('/{role}/edit', 'edit')->name('role.edit')->middleware('permission');
            Route::post('/{role}', 'update')->name('role.update')->middleware('permission');
            Route::delete('/{role}', 'destroy')->name('role.destroy')->middleware('permission');
        });
    });

    Route::prefix('reports')->as('reports.')->group(function () {
        Route::get('/sales/details', [SalesReportController::class, 'salesReportDetailsView'])->name('sales.details')->middleware('permission');
        Route::get('/sales/details/data', [SalesReportController::class, 'getSalesReportDetailsData'])->name('sales.details.data');
        Route::get('/sales/details/download', [SalesReportController::class, 'downloadSalesReportDetails'])->name('sales.details.download')->middleware('permission');
        
        Route::get('/expense/details', [ExpenseReportController::class, 'expenseReportDetailsView'])->name('expense.details')->middleware('permission');
        Route::get('/expense/details/data', [ExpenseReportController::class, 'getExpenseReportDetailsData'])->name('expense.details.data');
        Route::get('/expense/details/download', [ExpenseReportController::class, 'downloadExpenseReportDetails'])->name('expense.details.download')->middleware('permission');
        
        Route::get('/discount-adjustment/details', [DiscountAdjustmentReportController::class, 'discountAdjustmentReportView'])->name('discount-adjustment.details');
        Route::get('/discount-adjustment/details/data', [DiscountAdjustmentReportController::class, 'getDiscountAdjustmentReportData'])->name('discount-adjustment.details.data');
        Route::get('/discount-adjustment/details/download', [DiscountAdjustmentReportController::class, 'downloadDiscountAdjustmentReport'])->name('discount-adjustment.details.download');
        
        Route::get('/revenue/details', [RevenueReportController::class, 'revenueReportView'])->name('revenue.details')->middleware('permission');
        Route::get('/revenue/details/data', [RevenueReportController::class, 'getRevenueReportData'])->name('revenue.details.data');
        Route::get('/revenue/details/download', [RevenueReportController::class, 'downloadRevenueReport'])->name('revenue.details.download')->middleware('permission');
        
        Route::get('/net-profit/details', [NetProfitReportController::class, 'netProfitReportView'])->name('net-profit.details')->middleware('permission');
        Route::get('/net-profit/details/data', [NetProfitReportController::class, 'getNetProfitReportData'])->name('net-profit.details.data');
        Route::get('/net-profit/details/download', [NetProfitReportController::class, 'downloadNetProfitReport'])->name('net-profit.details.download')->middleware('permission');
        
        Route::get('/customer/details', [CustomerReportController::class, 'customerReportView'])->name('customer.details')->middleware('permission');
        Route::get('/customer/details/data', [CustomerReportController::class, 'getCustomerReportData'])->name('customer.details.data');
        Route::get('/customer/details/download', [CustomerReportController::class, 'downloadCustomerReport'])->name('customer.details.download')->middleware('permission');
        
        Route::get('/employee/details', [EmployeeReportController::class, 'employeeReportView'])->name('employee.details')->middleware('permission');
        Route::get('/employee/details/data', [EmployeeReportController::class, 'getEmployeeReportData'])->name('employee.details.data');
        Route::get('/employee/details/download', [EmployeeReportController::class, 'downloadEmployeeReport'])->name('employee.details.download')->middleware('permission');
        
        Route::get('/staff/details', [StaffReportController::class, 'staffReportView'])->name('staff.details')->middleware('permission');
        Route::get('/staff/details/data', [StaffReportController::class, 'getStaffReportData'])->name('staff.details.data');
        Route::get('/staff/details/download', [StaffReportController::class, 'downloadStaffReport'])->name('staff.details.download')->middleware('permission');
        
        Route::get('/sms/history', [SmsHistoryReportController::class, 'smsHistoryView'])->name('sms.history')->middleware('permission');
        Route::get('/sms/history/data', [SmsHistoryReportController::class, 'getSmsHistoryData'])->name('sms.history.data');
        Route::get('/sms/history/download', [SmsHistoryReportController::class, 'downloadSmsHistoryReport'])->name('sms.history.download')->middleware('permission');
    });

    Route::prefix('service')->as('service.')->group(function () {
        Route::prefix('category')->controller(CategoryController::class)->group(function () {
            Route::get('/', 'index')->name('category.index')->middleware('permission');
            Route::get('/create', 'create')->name('category.create');
            Route::post('/store', 'store')->name('category.store')->middleware('permission');
            Route::get('/{category}/edit', 'edit')->name('category.edit');
            Route::put('/{category}', 'update')->name('category.update')->middleware('permission');
            Route::delete('/{category}', 'destroy')->name('category.destroy')->middleware('permission');
        });

        Route::prefix('brand')->controller(BrandController::class)->group(function () {
            Route::get('/', 'index')->name('brand.index')->middleware('permission');
            Route::get('/create', 'create')->name('brand.create');
            Route::post('/store', 'store')->name('brand.store')->middleware('permission');
            Route::get('/{brand}/edit', 'edit')->name('brand.edit');
            Route::put('/{brand}', 'update')->name('brand.update')->middleware('permission');
            Route::delete('/{brand}', 'destroy')->name('brand.destroy')->middleware('permission');
        });

        Route::prefix('unit')->controller(UnitController::class)->group(function () {
            Route::get('/', 'index')->name('unit.index')->middleware('permission');
            Route::get('/create', 'create')->name('unit.create');
            Route::post('/store', 'store')->name('unit.store')->middleware('permission');
            Route::get('/{unit}/edit', 'edit')->name('unit.edit');
            Route::put('/{unit}', 'update')->name('unit.update')->middleware('permission');
            Route::delete('/{unit}', 'destroy')->name('unit.destroy')->middleware('permission');
        });

        Route::prefix('supplier')->controller(SupplierController::class)->group(function () {
            Route::get('/', 'index')->name('supplier.index');
            Route::get('/create', 'create')->name('supplier.create');
            Route::post('/store', 'store')->name('supplier.store');
            Route::get('/{supplier}', 'show')->name('supplier.show');
            Route::get('/{supplier}/edit', 'edit')->name('supplier.edit');
            Route::put('/{supplier}', 'update')->name('supplier.update');
            Route::delete('/{supplier}', 'destroy')->name('supplier.destroy');
        });

        Route::controller(ServiceController::class)->group(function () {
            Route::get('/', 'index')->name('index')->middleware('permission');
            Route::get('/datatable', 'datatable')->name('datatable');
            Route::get('/create', 'create')->name('create');
            Route::get('/{service}/copy', 'copy')->name('copy');
            Route::post('/store', 'store')->name('store')->middleware('permission');
            Route::get('/{service}', 'show')->name('show')->middleware('permission');
            Route::get('/{service}/edit', 'edit')->name('edit')->middleware('permission');
            Route::put('/{service}', 'update')->name('update')->middleware('permission');
            Route::delete('/{service}', 'destroy')->name('destroy')->middleware('permission');
        });
    });

    Route::prefix('product')->as('product.')->group(function () {
        Route::controller(ProductController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/datatable', 'datatable')->name('datatable');
            Route::get('/{product}/purchases', 'getProductPurchases')->name('get-purchases');
            Route::get('/{product}/edit', 'edit')->name('edit');
            Route::get('/{product}', 'show')->name('show');
            Route::post('/store', 'store')->name('store');
            Route::put('/{product}', 'update')->name('update');
            Route::delete('/{product}', 'destroy')->name('destroy');
        });

        Route::prefix('variation')->controller(VariationController::class)->group(function () {
            Route::get('/', 'index')->name('variation.index');
            Route::get('/datatable', 'datatable')->name('variation.datatable');
            Route::post('/store', 'store')->name('variation.store');
            Route::get('/{variation}/purchase-items', 'getPurchaseItems')->name('variation.purchase-items');
            Route::post('/{variation}/add-stock', 'addStockFromPurchaseItem')->name('variation.add-stock');
            Route::post('/{variation}/move-stock', 'moveStockToPurchase')->name('variation.move-stock');
            Route::get('/{variation}/price-update-info', 'getPriceUpdateInfo')->name('variation.price-update-info');
            Route::post('/{variation}/create-fresh-variant', 'createFreshVariant')->name('variation.create-fresh-variant');
            Route::get('/{variation}', 'show')->name('variation.show');
            Route::put('/{variation}', 'update')->name('variation.update');
            Route::delete('/{variation}', 'destroy')->name('variation.destroy');
        });
    });

    Route::prefix('stock')->as('stock.')->group(function () {
        Route::prefix('purchase')->controller(\App\Http\Controllers\Stock\PurchaseController::class)->group(function () {
            Route::get('/', 'index')->name('purchase.index');
            Route::get('/datatable', 'datatable')->name('purchase.datatable');
            Route::get('/create', 'create')->name('purchase.create');
            Route::post('/store', 'store')->name('purchase.store');
            Route::get('/{purchase}/edit', 'edit')->name('purchase.edit');
            Route::put('/{purchase}', 'update')->name('purchase.update');
            Route::get('/{purchase}', 'show')->name('purchase.show');
            Route::get('/variations/get', 'getProductVariations')->name('purchase.variations.get');
            Route::put('/item/{purchaseItem}', 'updatePurchaseItem')->name('purchase.item.update');
            Route::delete('/item/{purchaseItem}', 'removePurchaseItem')->name('purchase.item.remove');
        });
    });

    Route::prefix('sales')->as('sales.')->group(function () {
        Route::prefix('customer')->controller(CustomerController::class)->group(function () {
            Route::get('/', 'index')->name('customer.index')->middleware('permission');
            Route::get('/datatable', 'datatable')->name('customer.datatable');
            Route::get('/create', 'create')->name('customer.create');
            Route::post('/store', 'store')->name('customer.store')->middleware('permission');
            Route::get('/{customer}/edit', 'edit')->name('customer.edit')->middleware('permission');
            Route::put('/{customer}', 'update')->name('customer.update')->middleware('permission');
            Route::delete('/{customer}', 'destroy')->name('customer.destroy')->middleware('permission');
            Route::get('/{customer}/details', 'details')->name('customer.details')->middleware('permission');
            Route::get('/{customer}/info', 'getCustomerInfo')->name('customer.info');
        });

        Route::prefix('customer')->controller(LoyaltyCardController::class)->group(function () {
            Route::get('/{customer}/loyalty', 'loyalty')->name('customer.loyalty')->middleware('permission'); // customer loyalty details
            Route::post('/loyalty/cards', [LoyaltyCardController::class, 'store'])->name('customer.loyalty.cards.store')->middleware('permission'); // create new card
            Route::get('/loyalty/cards/{cardId}', [LoyaltyCardController::class, 'getCardHistory'])->name('customer.loyalty.cards.history'); // no permission need, access from pos page, and loyalty details apge
            Route::put('/loyalty/cards/{cardId}', [LoyaltyCardController::class, 'update'])->name('customer.loyalty.cards.update')->middleware('permission'); // update card
        });

        Route::prefix('sale')->controller(SaleController::class)->group(function () {
            Route::get('/', 'index')->name('sale.index')->middleware('permission');
            Route::get('/datatable', 'datatable')->name('sale.datatable');
            Route::get('/{sale}', 'show')->name('sale.show')->middleware('permission');
            Route::get('/modal/{sale}', 'modal')->name('sale.modal');
            Route::delete('/{sale}', 'destroy')->name('sale.destroy')->middleware('permission');
        });
    });

    Route::prefix('employee')->controller(EmployeeController::class)->group(function () {
        Route::get('/', 'index')->name('employee.index')->middleware('permission');
        Route::post('/store', 'store')->name('employee.store')->middleware('permission');
        Route::get('/{employee}/details', 'details')->name('employee.details')->middleware('permission');
        Route::get('/{employee}/edit', 'edit')->name('employee.edit')->middleware('permission');
        Route::put('/{employee}', 'update')->name('employee.update')->middleware('permission');
        Route::delete('/{employee}', 'destroy')->name('employee.destroy')->middleware('permission');
    });

    Route::prefix('employee-review')->controller(EmployeeReviewController::class)->group(function () {
        Route::post('/store', 'store')->name('employee.review.store')->middleware('permission');
        Route::put('/{review}', 'update')->name('employee.review.update')->middleware('permission');
        Route::delete('/{review}', 'destroy')->name('employee.review.destroy')->middleware('permission');
    });

    Route::prefix('attendance')->controller(AttendanceController::class)->group(function () {
        Route::get('/data', 'getAttendanceData')->name('attendance.data')->middleware('permission');
        Route::post('/save', 'saveAttendance')->name('attendance.save')->middleware('permission');
        Route::post('/mark-all-present', 'markAllPresent')->name('attendance.mark-all-present');
        Route::get('/designations', 'getDesignations')->name('attendance.designations');
        Route::get('/check-today-status', 'checkTodayAttendanceStatus')->name('attendance.check-today-status')->middleware('permission');
    });

    Route::prefix('pos')->as('pos.')->group(function () {
        Route::controller(PosController::class)->group(function () {
            Route::get('/', 'index')->name('index')->middleware('permission');
            Route::get('/account', 'getAccountInfo')->name('account.get');
            Route::get('/search/service', 'searchService')->name('search.service');
            Route::get('/staffs', 'getStaffs')->name('staffs');
            Route::post('/sales/save', 'saveSales')->name('sales.save');
            Route::get('/customer/last-sales/{customerId}', 'getCustomerLastSales')->name('customer.lastSales');
        });
        
        Route::prefix('customer')->controller(CustomerController::class)->group(function () {
            Route::get('/search', 'search')->name('customer.search');
        });

        Route::prefix('customer')->controller(LoyaltyCardController::class)->group(function () {
            Route::get('/loyalty/status/{customerId}','getLoyaltyStatus')->name('customer.loyalty.status');
            Route::post('/loyalty/verify', 'verifyCardAndGetHistory')->name('customer.loyalty.verify');
        });
    });

    Route::prefix('dashboard')->as('dashboard.')->controller(HomeController::class)->group(function () {
        Route::get('/', 'index')->name('dashboard');
        Route::post('/filter/data', 'filterData')->name('filter.data');
        Route::post('/fixed/metrics', 'fixedMetrics')->name('fixedmetrics.data');
    });

    Route::prefix('setup')->as('setup.')->group(function () {
        Route::prefix('profile')->group(function () {
            Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::post('/update-info', [ProfileController::class, 'updateInfo'])->name('profile.updateInfo');
            Route::post('/update-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
        });
    });

    // help portal 
    Route::prefix('help-portal')->as('help.')->group(function () {
        Route::prefix('loyalty')->group(function () {
            Route::get('/', [LoyaltyHelpController::class, 'index'])->name('setup.loyalty');
        });
    });

});

require __DIR__.'/auth.php';
