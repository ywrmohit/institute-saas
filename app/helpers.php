<?php

use App\Models\SystemSetting;

if (!function_exists('setting')) {
    /**
     * Get or set a system setting with caching.
     *
     * @param string|array $key
     * @param mixed $default
     * @param int|null $franchiseId
     * @return mixed
     */
    function setting($key, $default = null, ?int $franchiseId = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                SystemSetting::set($k, $v, $franchiseId);
            }
            return null;
        }

        return SystemSetting::get($key, $franchiseId, $default);
    }
}
