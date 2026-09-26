<?php

namespace App\Mechanisms;

use Livewire\Mechanisms\HandleRequests\HandleRequests;

class CustomHandleRequests extends HandleRequests
{
    public function getUpdateUri()
    {
        $route = $this->updateRoute ?? $this->findUpdateRoute();

        $path = app('url')->toRoute($route, [], false);
        $base = request()->getBaseUrl();

        if ($base && !str_starts_with($path, $base)) {
            $path = rtrim($base, '/') . '/' . ltrim($path, '/');
        }

        return (string) str($path)->start('/');
    }
}
