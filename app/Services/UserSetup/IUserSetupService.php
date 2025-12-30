<?php
namespace App\Services\UserSetup;

interface IUserSetupService{
    public function getUsers($POSID);
    public function getUser($POSID, $userid);
    public function createUser($user);
    public function updateUser($user);
    public function delete($POSID, $userid);
}
