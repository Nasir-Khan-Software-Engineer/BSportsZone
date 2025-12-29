<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ini_set('mbstring.internal_encoding', 'UTF-8');
        ini_set('default_charset', 'UTF-8');

        //dd("ok");
        // If user is logged in, get role and share to views
        if (auth()->check()) {
            $role = auth()->user()->role;

            dd($role);
            // store role in session
            session()->put('role', $role);

            // share the role to all views
            View::share('role', $role);
        }

        // Share shop data with all views
       // $shops = DB::table('shops')->get();
        // Or using Eloquent: $shops = Shop::all();

        View::share('navShops', []);

        View::composer('*', function ($view) {
            if (Auth::check()) {
                $view->with('authUser', Auth::user());
            }
        });
    }
}
