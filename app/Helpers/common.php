<?php

use Illuminate\Support\Facades\Session;

if (!function_exists('getLoyaltySettings')) {
    /**
     * Returns loyalty settings of the logged-in user from session
     */
    function getLoyaltySettings(): array
    {
        return session('loyaltySettings', []);
    }
}




if (!function_exists('getAccountInfoSettings')) {
    /**
     * Returns loyalty settings of the logged-in user from session
     */
    function getAccountInfoSettings(): array
    {
        return session('accountInfo', []);
    }
}
