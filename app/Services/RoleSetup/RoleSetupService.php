<?php
namespace App\Services\RoleSetup;
use Illuminate\Http\Request;
use App\Models\Role;

class RoleSetupService implements IRoleSetupService{

    public function store(Request $request){
        $POSID = auth()->user()->POSID;

        $Role = new Role();
        $Role->POSID            = $POSID;
        $Role->name             = $request->name;
        $Role->description      = $request->description;
        $Role->permissions      = $request->permissions;
        $Role->created_by       = auth()->user()->id;
        $Role->isActive         = 1;
        $Role->save();
        return $Role;
    }
    
    public function update(Request $request, $id){

        $POSID = auth()->user()->POSID;
        $Role = Role::where('POSID', '=', $POSID)
                    ->where('id', '=', $id)
                    ->first();
        
        $Role->name             = $request->name;
        $Role->description      = $request->description;
        $Role->permissions      = $request->permissions;
        $Role->isActive         = $request->isActive;
        $Role->updated_by       = auth()->user()->id;

        $Role->save();
        return $Role;
    }
}