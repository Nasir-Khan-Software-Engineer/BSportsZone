<?php

namespace App\Providers;

use App\Repositories\Brand\BrandRepository;
use App\Repositories\Brand\IBrandRepository;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\Category\ICategoryRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\AccountSetup\IAccountSetupRepository;
use App\Repositories\AccountSetup\AccountSetupRepository;
use App\Repositories\UserSetup\IUserSetupRepository;
use App\Repositories\UserSetup\UserSetupRepository;
use App\Repositories\ShopSetup\IShopSetupRepository;
use App\Repositories\ShopSetup\ShopSetupRepository;
use App\Repositories\Pos\PosRepository;
use App\Repositories\Pos\IPosRepository;

class RepositoryLayerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->scoped(IAccountSetupRepository::class, AccountSetupRepository::class);
        $this->app->scoped(IUserSetupRepository::class, UserSetupRepository::class);
        $this->app->scoped(IShopSetupRepository::class, ShopSetupRepository::class);
        $this->app->scoped(ICategoryRepository::class, CategoryRepository::class);
        $this->app->scoped(IBrandRepository::class, BrandRepository::class);
        $this->app->scoped(IPosRepository::class, PosRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
