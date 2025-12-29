<?php
namespace App\Repositories\UserSetup;

interface IUserSetupRepository{
    public function  getUsers($posid);
    public function getUser($posid, $userid);
    public function store($user);
    public function update($user);
    public function delete($posid, $userid);
}
