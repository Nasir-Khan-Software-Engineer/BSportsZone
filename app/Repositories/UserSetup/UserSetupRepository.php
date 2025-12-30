<?php
namespace App\Repositories\UserSetup;

use App\Models\User;

class UserSetupRepository implements IUserSetupRepository{

    public function  getUsers($POSID)
    {
        return User::where('POSID', $POSID)->where('user_type', 'pos_user')->get();
    }

    public function getUser($POSID, $userid){
        return User::where('POSID', $POSID)->where('id', $userid)->where('user_type', 'pos_user')->first();
    }

    public function store($user){
         return User::create($user);
    }

    public function update($user){
        return User::where('POSID', $user['POSID'])->where('id', $user['id'])->where('user_type', 'pos_user')->update($user['userInfo']);
    }

    public function delete($POSID, $userid){
        return User::where('POSID', '=', $POSID)->where('id', '=', $userid)->where('user_type', 'pos_user')->delete();
    }
}
