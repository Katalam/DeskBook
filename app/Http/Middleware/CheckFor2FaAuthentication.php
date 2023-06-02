<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFor2FaAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure(Request): (Response) $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->hasEnabledTwoFactorAuthentication()) {
            $request->session()->flash('flash.twofa', false);
        } else {
            $request->session()->flash('flash.twofa');
        }

        return $next($request);
    }
}
