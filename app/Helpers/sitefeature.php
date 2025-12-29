<?php

use Illuminate\Support\Facades\Session;

if (!function_exists('isFeatureEnabled')) {
    function isFeatureEnabled(string $featureName): bool
    {
        $features = session('site_features', []);
        return collect($features)->contains(fn($f) => $f['feature_name'] === $featureName);
    }
}

if (!function_exists('getSiteFeatureNames')) {
    function getSiteFeatureNames(): array
    {
        return collect(session('site_features', []))
               ->pluck('feature_name')
               ->toArray();
    }
}