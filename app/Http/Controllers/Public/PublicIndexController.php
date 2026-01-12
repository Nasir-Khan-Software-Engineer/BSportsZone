<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PublicIndexController extends Controller
{
    public function index()
    {
        return view('public.page.index');
    }

    public function shop()
    {
        return view('public.page.shop');
    }
}
