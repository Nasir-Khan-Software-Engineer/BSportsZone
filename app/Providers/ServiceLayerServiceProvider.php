<?php

namespace App\Providers;

use App\Services\Pos\IPosService;
use App\Services\Pos\PosService;
use Illuminate\Support\ServiceProvider;
use App\Services\AccountSetup\IAccountSetupService;
use App\Services\AccountSetup\AccountSetupService;
use App\Services\UserSetup\IUserSetupService;
use App\Services\UserSetup\UserSetupService;
use App\Services\ShopSetup\IShopSetupService;
use App\Services\ShopSetup\ShopSetupService;
use App\Services\RoleSetup\IRoleSetupService;
use App\Services\RoleSetup\RoleSetupService;
use App\Services\Brand\BrandService;
use App\Services\Brand\IBrandService;
use App\Services\Category\CategoryService;
use App\Services\Category\ICategoryService;
use App\Services\Setup\Customer\ICustomerService;
use App\Services\Setup\Customer\CustomerService;
use App\Services\Pos;
use App\Services\Report\IReportService;
use App\Services\Report\ReportService;
use App\Services\Service\IServiceService;
use App\Services\Service\ServiceService;
use App\Services\Expense\IExpenseService;
use App\Services\Expense\ExpenseService;
use App\Services\Loyalty\ILoyaltyService;
use App\Services\Loyalty\LoyaltyService;
use App\Services\Sms\SMSServiceInterface;
use App\Services\Sms\SMSService;
use App\Services\Purchase\IPurchaseService;
use App\Services\Purchase\PurchaseService;
use App\Services\Product\IProductService;
use App\Services\Product\ProductService;

class ServiceLayerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->scoped(IAccountSetupService::class, AccountSetupService::class);
        $this->app->scoped(IUserSetupService::class, UserSetupService::class);
        $this->app->scoped(IShopSetupService::class, ShopSetupService::class);
        $this->app->scoped(IRoleSetupService::class, RoleSetupService::class);
        $this->app->scoped(ICategoryService::class, CategoryService::class);
        $this->app->scoped(IBrandService::class, BrandService::class);
        $this->app->scoped(ICustomerService::class, CustomerService::class);
        $this->app->scoped(IPosService::class, PosService::class);
        $this->app->scoped(IReportService::class, ReportService::class);
        $this->app->scoped(IServiceService::class, ServiceService::class);
        $this->app->scoped(IExpenseService::class, ExpenseService::class);
        $this->app->scoped(ILoyaltyService::class, LoyaltyService::class);
        $this->app->scoped(SMSServiceInterface::class, SMSService::class);
        $this->app->scoped(IPurchaseService::class, PurchaseService::class);
        $this->app->scoped(IProductService::class, ProductService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
