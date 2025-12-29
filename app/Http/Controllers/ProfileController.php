<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Exception;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {

        $user = $request->user();

        $accessRights = $user->accessRights();
        //dd($accessRights);
        $groupedAccessRights = $accessRights->groupBy(function ($accessRight) {
            // Extract module from route name (e.g., 'setup.user.index' -> 'Setup')
            $parts = explode('.', $accessRight->route_name);
            return ucfirst($parts[0] ?? 'Other');
        });

        return view('profile.edit', [
            'user' => $user,
            'role' => $user->role,
            'groupedAccessRights' => $groupedAccessRights
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updateInfo(Request $request)
    {
        try {
            $user = auth()->user();

            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required', 'email', 'max:255',
                    Rule::unique('users')->ignore($user->id),
                ],
            ]);

            $user->update([
                'name' => $data['name'],
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Profile information updated successfully.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $user = auth()->user();

            $data = $request->validate([
                'old_password' => ['required'],
                'password' => [
                    'required', 
                    'confirmed', 
                    'min:8',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
                ],
            ], [
                'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
                'password.confirmed' => 'Password confirmation does not match.',
            ]);

            if (!Hash::check($data['old_password'], $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'errors' => ['old_password' => ['Old password does not match']],
                ], 422);
            }

            $user->password = Hash::make($data['password']);
            $user->save();

            auth()->logout();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Password updated successfully.'
            ]);
        }catch(ValidationException $exception){
            return response()->json(
                [
                    'status'=>'error', 
                    'message' => '', 
                    'errors' => $exception->validator->errors()
                ]
            );
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }
}
