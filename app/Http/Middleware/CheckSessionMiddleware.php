<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\POSSettings;
use App\Models\Accountinfo;
use App\Models\LoyaltySetting;
use App\Models\SmsConfig;
class CheckSessionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {


        // Auth::logout();

        //         $request->session()->invalidate();
        //         $request->session()->regenerateToken();
        
        // only pos user can access this system
        if (Auth::check() && auth()->user()->user_type !== 'pos_user') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login');
        }

        // store access rights in session

        if (Auth::check()) {

            // store site features
            if (!session()->has('site_features')) {
                $posid = auth()->user()->posid;
                $pos = Accountinfo::with('sitefeatures')->where('POSID', $posid)->first();
                $formated_features = $pos->sitefeatures->map(function($feature) {
                    return [
                        'feature_name' => $feature->feature_name
                    ];
                })->toArray();
                session(['site_features' => $formated_features]);
            }

            // store role name and access rights
            if (!session()->has('role_name') || !session()->has('access_rights')) {
                $role = auth()->user()->role;
                $accessRights = $role->accessRights->map(function($right) {
                    return [
                        'title' => $right->title,
                        'route_name' => $right->route_name,
                        'short_id' => $right->short_id,
                    ];
                })->toArray();

                

                //$accessRights = addLocalAccessRights($accessRights);

                //dd($accessRights);
                
                session([
                    'role_name' => $role->name,
                    'access_rights' => $accessRights,
                ]);
            }
        }

        // Only run if user is authenticated
        if (Auth::check() && !session()->has('accountInfo')) {
            $user = Auth::user();
            
            $accountInfo = $user->accountInfo;

            if (!$accountInfo) {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'account' => 'Your account information could not be found. Please contact support.',
                ]);
            }

            if ($accountInfo) {
                session([
                    'accountInfo' => [
                        'posid'               => $user->posid,
                        'companyName'         => $accountInfo->companyName,
                        'logo'                => $accountInfo->logo,
                        'primaryEmail'        => $accountInfo->primaryEmail,
                        'primaryPhone'        => $accountInfo->primaryPhone,
                        'address'             => $accountInfo->address,
                        'serviceCodePrefix'   => $accountInfo->productCodePrefix,
                        'invoiceNumberPrefix' => $accountInfo->invoiceNumberPrefix,
                        'timezone'            => 'Asia/Dhaka',
                        'currency'            => 'BDT',
                        'shopid'              => 1,
                    ]
                ]);
            }
        }


        if (Auth::check() && !session()->has('posSettings')) {
            $posid = auth()->user()->posid;
            $posSettings = POSSettings::where('posid', $posid)->first();

            if (!$posSettings) {
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'account' => 'Your POS Settings information could not be found. Please contact support.',
                ]);
            }

            if ($posSettings) {
                session([
                    'posSettings' => [
                        'adjustment_min' => $posSettings->adjustment_min,
                        'adjustment_max' => $posSettings->adjustment_max,
                    ]
                ]);
            }
        }

        if (Auth::check() && !session()->has('loyaltySettings') && isFeatureEnabled('ENABLED_LOYALTY')) {
            $posid = auth()->user()->posid;
            $loyaltySettings = LoyaltySetting::where('posid', $posid)->first();

            if ($loyaltySettings) {
                session([
                    'loyaltySettings' => [
                        'minimum_sales_amount' => $loyaltySettings->minimum_sales_amount,
                        'validity_period_months' => $loyaltySettings->validity_period_months,
                        'max_visits' => $loyaltySettings->max_visits,
                        'max_visits_per_day' => $loyaltySettings->max_visits_per_day,
                        'rules_text' => $loyaltySettings->rules_text,
                        'status' => $loyaltySettings->status,
                        'minimum_sales_amount_applies_for' => "All",
                        'minimum_sales_amount_applies_for' => $loyaltySettings->minimum_sales_amount_applies_for
                    ]
                ]);
            }
        }

        // Load SMS configuration into session
        if (Auth::check() && !session()->has('sms_config')) {
            $posid = auth()->user()->posid;
            $smsConfig = SmsConfig::where('posid', $posid)
                ->where('is_active', true)
                ->first();

            if ($smsConfig) {
                session([
                    'sms_config' => [
                        'base_url' => $smsConfig->base_url,
                        'username' => $smsConfig->username,
                        'api_key' => $smsConfig->api_key,
                        'sender_id' => $smsConfig->sender_id,
                        'campaign_id' => $smsConfig->campaign_id,
                        'is_active' => $smsConfig->is_active
                    ]
                ]);
            } else {
                // Fallback to config file if no DB config found
                session([
                    'sms_config' => [
                        'base_url' => config('sms.base_url'),
                        'username' => config('sms.username'),
                        'api_key' => config('sms.apikey'),
                        'sender_id' => config('sms.sender'),
                        'campaign_id' => config('sms.campaign_id', 'null'),
                        'is_active' => false
                    ]
                ]);
            }
        }

        return $next($request);
    }
}
