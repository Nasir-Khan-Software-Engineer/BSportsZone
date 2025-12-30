<?php
namespace App\Repositories\UserSetup;

interface IUserSetupRepository{
    public function  getUsers($POSID);
    public function getUser($POSID, $userid);
    public function store($user);
    public function update($user);
    public function delete($POSID, $userid);
}
