<?php
namespace App\Repositories\AccountSetup;

interface IAccountSetupRepository{
    public function getAccountInfo($POSID);
    public function updateAccountInfo($accountInfo);
}
