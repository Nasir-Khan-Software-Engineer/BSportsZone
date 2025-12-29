<?php
namespace App\Services\UserSetup;

use App\Repositories\UserSetup\IUserSetupRepository;

class UserSetupService implements IUserSetupService{
    private IUserSetupRepository $userSetupRepository;

    public function __construct(IUserSetupRepository $userSetupRepository){

        $this->userSetupRepository = $userSetupRepository;
    }

    public function getUsers($posid){
        return $this->userSetupRepository->getUsers($posid);
    }

    public function getUser($posid, $userid){
        return $this->userSetupRepository->getUser($posid, $userid);
    }

    public function createUser($user){
        $user['posid'] = auth()->user()->posid;
        $user['defaultshopid'] = 1;
        return $this->userSetupRepository->store($user);
    }

    public function updateUser($user){
        return $this->userSetupRepository->update($user);
    }

    public function delete($posid, $userid){
        // verify that user can deletable
        // otherwise deactivate the user

        return $this->userSetupRepository->delete($posid, $userid);
    }
}
