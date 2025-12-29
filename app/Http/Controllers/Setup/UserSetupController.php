<?php
namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\UserSetup\IUserSetupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Mockery\Exception;

class UserSetupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    //private $userSetupService;

    public function __construct(IUserSetupService $iUserSetupService)
    {
        $this->userSetupService = $iUserSetupService;
    }

    public function index()
    {
        $users = $this->userSetupService->getUsers(auth()->user()->posid);

        foreach ($users as $user) {
            $user->formattedDate = formatDate($user->created_at);
            $user->formattedTime = formatTime($user->created_at);
        }
        return view('setup/userSetup/index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $role_id = auth()->user()->role_id;
        $posid   = auth()->user()->posid;
        $roles   = Role::where('posid', $posid)->get();
        return view('setup/userSetup/create', ['roles' => $roles]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $posid = auth()->user()->posid;

        $request->validate([
            'name'                  => 'required|string|min:3|max:100',

            'email'                 => [
                'required',
                'email',
                'unique:users,email',
                Rule::unique('users')->where(function ($query) use ($posid) {
                    return $query->where('posid', $posid);
                }),
            ],

            'phone'                 => [
                'required',
                'string',
                'size:11',
                'regex:/^01[0-9]{9}$/',
                Rule::unique('users')->where(function ($query) use ($posid) {
                    return $query->where('posid', $posid);
                }),
            ],

            'role_id'               => 'required|exists:roles,id',

            'password'              => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
            ],

            'password_confirmation' => 'required|same:password',
        ], [
            'password.regex'             => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'phone.regex'                => 'Phone number must be a valid 11-digit number starting with 01.',
            'phone.size'                 => 'Phone number must be exactly 11 digits.',
            'role_id.exists'             => 'Please select a valid role.',
            'password_confirmation.same' => 'Password confirmation does not match.',
        ]);

        $user                = new User;
        $user->posid         = $posid;
        $user->name          = $request->name;
        $user->email         = $request->email;
        $user->password      = Hash::make($request->password);
        $user->phone         = $request->phone;
        $user->role_id       = $request->role_id;
        $user->user_type     = 'pos_user';
        $user->defaultshopid = 1;

        $user->save();

        return redirect()->back()->with('message', 'User created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $posid = auth()->user()->posid;
        $roles = Role::where('posid', $posid)->get();
        $user  = $this->userSetupService->getUser($posid, $id);
        return view('setup/userSetup/edit', ['user' => $user, 'roles' => $roles]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $posid = auth()->user()->posid;
        $request->validate([
            'name'    => 'required|string|min:3|max:100',
            'phone'   => [
                'required',
                'min:11',
                'max:11',
                Rule::unique('users')->where(function ($query) use ($posid, $user) {
                    return $query->where('posid', $posid)->where('id', '!=', $user->id);
                }),
            ],
            'role_id' => 'required',
        ]);

        $user->name    = $request->name;
        $user->phone   = $request->phone;
        $user->role_id = $request->role_id;

        $user->update();

        return redirect()->back()->with('message', 'User created successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $userCount = $this->userSetupService->delete(auth()->user()->posid, $id);

            if ($userCount > 0) {
                return response()->json(
                    [
                        'status'  => 'success',
                        'message' => 'User delete successfully.',
                    ]
                );
            } else {
                return response()->json(
                    [
                        'status'  => 'error',
                        'message' => 'Something went wrong, please try later.',
                    ]
                );
            }
        } catch (Exception $exception) {
            return $exception;
        }
    }
}
