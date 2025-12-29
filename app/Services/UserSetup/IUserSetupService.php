<?php
namespace App\Services\UserSetup;

interface IUserSetupService{
    public function getUsers($posid);
    public function getUser($posid, $userid);
    public function createUser($user);
    public function updateUser($user);
    public function delete($posid, $userid);
}
