<?php

namespace App\Services\AccountSetup;
use Illuminate\Http\Request;
interface IAccountSetupService{
    public function getAccountInfo($POSID);
    public function updateAccountInfo(Request $r);
}
