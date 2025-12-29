<?php

namespace App\Http\Controllers\HelpPortal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoyaltyHelpController extends Controller
{
    public function index()
    {
        return view('help-portal.setup.loyalty');
    }
}
