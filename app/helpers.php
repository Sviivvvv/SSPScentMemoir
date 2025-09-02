<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('banner_url')) {
    function banner_url(string $key): ?string
    {
        $map = config('banners.map', []);
        $entry = $map[$key] ?? null;
        if (!$entry)
            return null;

        $storagePath = $entry['storage'] ?? null;
        $fallbackPath = $entry['fallback'] ?? null;

        if ($storagePath && Storage::disk('public')->exists($storagePath)) {
            return Storage::url($storagePath);
        }
        return $fallbackPath ? asset($fallbackPath) : null;
    }
}
