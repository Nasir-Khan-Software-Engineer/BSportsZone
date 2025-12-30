<?php
namespace App\Services\UserSetup;

use App\Repositories\UserSetup\IUserSetupRepository;

class UserSetupService implements IUserSetupService{
    private IUserSetupRepository $userSetupRepository;

    public function __construct(IUserSetupRepository $userSetupRepository){

        $this->userSetupRepository = $userSetupRepository;
    }

    public function getUsers($POSID){
        return $this->userSetupRepository->getUsers($POSID);
    }

    public function getUser($POSID, $userid){
        return $this->userSetupRepository->getUser($POSID, $userid);
    }

    public function createUser($user){
        $user['POSID'] = auth()->user()->POSID;
        $user['defaultshopid'] = 1;
        return $this->userSetupRepository->store($user);
    }

    public function updateUser($user){
        return $this->userSetupRepository->update($user);
    }

    public function delete($POSID, $userid){
        // verify that user can deletable
        // otherwise deactivate the user

        return $this->userSetupRepository->delete($POSID, $userid);
    }
}
