<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CustomerRibbon extends Component
{
    public $customer;

    public function __construct($customer = [])
    {
        $this->customer = $customer;
    }

    public function render()
    {
        return view('components.customer-ribbon');
    }
}
