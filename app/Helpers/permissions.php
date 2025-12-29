<?php

use Illuminate\Support\Facades\Session;

if (!function_exists('hasAccess')) {
    /**
     * Check if the current logged-in user has access to a route or permission.
     *
     * @param string $routeNameOrPermission
     * @return bool
     */
    function hasAccess(string $routeNameOrPermission): bool
    {
        $accessRights = Session::get('access_rights', []);

        // Check by both route_name and short_id
        foreach ($accessRights as $right) {
            if (($right['route_name'] ?? '') === $routeNameOrPermission || 
                ($right['short_id'] ?? '') === $routeNameOrPermission) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('maskPhoneNumber')) {
    /**
     * Mask customer phone number showing only the last three digits.
     * Example: 01712345678 -> XXXXXXXX678
     *
     * @param string $phoneNumber
     * @return string
     */
    function maskPhoneNumber(string $phoneNumber): string
    {
        if (empty($phoneNumber) || strlen($phoneNumber) < 3) {
            return $phoneNumber;
        }
        
        $length = strlen($phoneNumber);
        $lastThree = substr($phoneNumber, -3);
        $maskedPart = str_repeat('X', $length - 3);
        
        return $maskedPart . $lastThree;
    }
}


if (!function_exists('addLocalAccessRights')) {
    function addLocalAccessRights(array $accessRights): array
    {
        // Hard-coded logical access rights
        $logicalAccessRights = [
            // if has => then add these
            'sales.customer.details' => [
                [
                    'title' => 'Show customer information modal (based on customer details permission)',
                    'route_name' => 'sales.customer.info',
                    'short_id' => 'customer_info',
                ],
            ]
        ];

        $existingRoutes = collect($accessRights)->pluck('route_name')->filter()->toArray();
        $newRights = $accessRights;

        foreach ($logicalAccessRights as $baseRoute => $extraRights) {
            if (in_array($baseRoute, $existingRoutes)) {
                foreach ($extraRights as $right) {
                    if (!in_array($right['route_name'], $existingRoutes)) {
                        $newRights[] = $right;
                    }
                }
            }
        }

        return $newRights;
    }
}


if (!function_exists('getUserPermissionNames')) {
    /**
     * Returns all route names (permissions) of the logged-in user from session
     */
    function getUserPermissionNames(): array
    {
        $accessRights = session('access_rights', []);

        // Extract only route names
        $routeNames = collect($accessRights)->pluck('route_name')->filter()->toArray();

        return $accessRights;
    }
}