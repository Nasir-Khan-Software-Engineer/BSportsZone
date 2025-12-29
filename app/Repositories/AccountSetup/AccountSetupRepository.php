<?php

namespace App\Repositories\AccountSetup;
use App\Models\Accountinfo;
use DB;

class AccountSetupRepository implements IAccountSetupRepository{
    public function __construct(){
        // inject dependencies
    }

    public function getAccountInfo($POSID){
        $thisAccountInfo = AccountInfo::with('posSettings', 'loyaltySettings')->where('posid', $POSID)->first();
        return $thisAccountInfo;
    }

    public function updateAccountInfo($accountInfo){
        
        $thisAccountInfo = Accountinfo::where('POSID',$accountInfo['POSID'])->first();
        
        $thisAccountInfo->companyName       = $accountInfo['companyName'];
        if(isset($accountInfo['logo']) && $accountInfo['logo'] != null){
            $thisAccountInfo->logo              = $accountInfo['logo'];
        }
        $thisAccountInfo->primaryEmail      = $accountInfo['primaryEmail'];
        $thisAccountInfo->secoundaryEmail   = $accountInfo['secoundaryEmail'];
        $thisAccountInfo->primaryPhone    = $accountInfo['primaryPhone'];
        $thisAccountInfo->secondaryPhone    = $accountInfo['secondaryPhone'];
        $thisAccountInfo->division          = $accountInfo['division'];
        $thisAccountInfo->district           = $accountInfo['district'];
        $thisAccountInfo->area              = $accountInfo['area'];
        $thisAccountInfo->address           = $accountInfo['address'];
        $thisAccountInfo->save();

        return $thisAccountInfo;
    }
}