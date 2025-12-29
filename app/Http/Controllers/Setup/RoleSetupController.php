<?php
namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\AccessRight;
use App\Models\Role;
use App\Services\RoleSetup\IRoleSetupService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleSetupController extends Controller
{
    public function __construct(IRoleSetupService $roleSetupService)
    {
        // $this->middleware('auth');
        $this->roleSetupService = $roleSetupService;
    }

    public function index()
    {
        $posid        = auth()->user()->posid;
        $roles        = Role::where('posid', '=', $posid)->get();
        $accessRights = AccessRight::all();
        return view('setup/role/index', ['roles' => $roles, 'accessRights' => $accessRights]);
    }

    public function show($id)
    {
        $posid = auth()->user()->posid;
        $role  = Role::where('posid', $posid)
            ->where('id', $id)
            ->firstOrFail();

        $assignedAccess = $role->accessRights->toArray();

        return response()->json([
            'role'            => $role,
            'allAccessRights' => $assignedAccess,
            'status'          => 'success',
        ]);
    }

    public function create()
    {
        $allAccessRights = AccessRight::all();
        return view('setup/role/create', [
            'allAccessRights' => $allAccessRights,
        ]);
    }

    public function edit($id)
    {
        $posid = auth()->user()->posid;

        $role = Role::where('posid', $posid)
            ->where('id', $id)
            ->firstOrFail();

        $allAccessRights = AccessRight::all();
        $assignedIds = $role->accessRights->pluck('id')->toArray();
        $assignedAccessRights   = $allAccessRights->whereIn('id', $assignedIds);
        $unassignedAccessRights = $allAccessRights->whereNotIn('id', $assignedIds);

        return view('setup/role/edit', [
            'role'                   => $role,
            'assignedAccessRights'   => $assignedAccessRights,
            'unassignedAccessRights' => $unassignedAccessRights,
        ]);
    }

    public function store(Request $request)
    {
        $posid = auth()->user()->posid;
        $validated = $request->validate([
            'name'           => [
                'required',
                'string',
                'min:3',
                Rule::unique('roles')->where('posid', $posid),
            ],
            'description'    => 'nullable|string|min:3',
            'assignedAccess' => 'required|array',
        ]);

        $role = Role::create([
            'POSID'       => $posid,
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'created_by'  => auth()->id(),
        ]);

        $role->accessRights()->sync($validated['assignedAccess']);
        return back()->with('message', 'Role created successfully.');

    } // end create

    public function update(Request $request, $id)
    {
        $posid = auth()->user()->posid;
        $validated = $request->validate([
            'name'           => [
                'required',
                'string',
                'min:3',
                Rule::unique('roles')->where('posid', $posid)->ignore($id),
            ],
            'description'    => 'nullable|string|min:3',
            'assignedAccess' => 'required|array',
        ]);

        $role = Role::where('posid', $posid)
            ->where('id', $id)
            ->firstOrFail();

        $role->update([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        $role->accessRights()->sync($validated['assignedAccess']);
        return back()->with('message', 'Role updated successfully.');
    } // end update

    public function destroy($id)
    {
        try {
            $posid = auth()->user()->posid;

            $role = Role::where('posid', $posid)
                ->where('id', $id)
                ->first();

            if (! $role) {
                return response()->json([
                    'status' => 'error',
                    'errors' => [
                        'Dependent' => ['Role not found.'],
                    ],
                ], 404);
            }

            if ($role->users()->count() > 0) {
                return response()->json([
                    'status' => 'error',
                    'errors' => [
                        'Dependent' => ['This role has assigned users.'],
                    ],
                ]);
            }

            $role->accessRights()->detach();

            $role->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Role deleted successfully.',
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'errors' => [
                    'Dependent' => $exception->getMessage(),
                ],
            ]);
        }
    }
}
