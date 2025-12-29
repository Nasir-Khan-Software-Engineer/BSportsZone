<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
class UserPermission
{
    public function handle(Request $request, Closure $next): Response
    {

        $accessRights = session('access_rights', []);
        $thisRoute = strtolower($request->route()->getName());

        //dd the access right and thsi route
        // dd($accessRights, $thisRoute);

        $hasAccess = collect($accessRights)->contains(function($right) use ($thisRoute) {
            return $right['route_name'] === $thisRoute || $right['short_id'] === $thisRoute;
        });

        //$hasAccess = true; // for now always true
        
        if (!$hasAccess) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'failed',
                    'errors' => [
                        'permission' => ['Forbidden: You do not have permission to access this resource.']
                    ]
                ]);
            }
            abort(403, 'Forbidden: You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
