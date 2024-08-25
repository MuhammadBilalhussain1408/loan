<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (tenant()) {
            if (!tenant('active') && !$request->is('setting/subscription*')) {
                session()->flash('error', 'Your account has been deactivated');
                return redirect()->route('settings.subscriptions.index');
            }
        }

        return $next($request);
    }
}
