<?php
namespace App\Services\RoleSetup;
use Illuminate\Http\Request;
use App\Models\Role;

class RoleSetupService implements IRoleSetupService{

    public function store(Request $request){
        $posid = auth()->user()->posid;

        $Role = new Role();
        $Role->POSID            = $posid;
        $Role->name             = $request->name;
        $Role->description      = $request->description;
        $Role->permissions      = $request->permissions;
        $Role->created_by       = auth()->user()->id;
        $Role->isActive         = 1;
        $Role->save();
        return $Role;
    }
    
    public function update(Request $request, $id){

        $posid = auth()->user()->posid;
        $Role = Role::where('posid', '=', $posid)
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