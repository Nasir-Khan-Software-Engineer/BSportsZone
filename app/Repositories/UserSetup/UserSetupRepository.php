<?php
namespace App\Repositories\UserSetup;

use App\Models\User;

class UserSetupRepository implements IUserSetupRepository{

    public function  getUsers($posid)
    {
        return User::where('posid', $posid)->where('user_type', 'pos_user')->get();
    }

    public function getUser($posid, $userid){
        return User::where('posid', $posid)->where('id', $userid)->where('user_type', 'pos_user')->first();
    }

    public function store($user){
         return User::create($user);
    }

    public function update($user){
        return User::where('posid', $user['posid'])->where('id', $user['id'])->where('user_type', 'pos_user')->update($user['userInfo']);
    }

    public function delete($posid, $userid){
        return User::where('posid', '=', $posid)->where('id', '=', $userid)->where('user_type', 'pos_user')->delete();
    }
}
