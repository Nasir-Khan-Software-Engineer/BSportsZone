<?php
namespace App\Services\AccountSetup;
use App\Repositories\AccountSetup\IAccountSetupRepository;
use Illuminate\Http\Request;

class AccountSetupService implements IAccountSetupService{
    public function __construct(IAccountSetupRepository $accountSetupRepository){
        // inject dependency
        $this->accountSetupRepository = $accountSetupRepository;
    }

    public function getAccountInfo($POSID){
        return $this->accountSetupRepository->getAccountInfo($POSID);
    }
    
    public function updateAccountInfo($accountInfo){

        return $this->accountSetupRepository->updateAccountInfo($accountInfo);
    }

    public function loyaltySettings()
    {
        return $this->hasOne(LoyaltySettings::class, 'posid', 'posid');
    }
}