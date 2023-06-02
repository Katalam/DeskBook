<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'can' => [
                'canAccessUserManagement' => $request->user()?->can('canAccessUserManagement'),
                'canAccessTableManagement' => $request->user()?->can('canAccessTableManagement'),
            ],
        ]);
    }
}
