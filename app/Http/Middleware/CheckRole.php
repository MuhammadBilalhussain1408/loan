<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    protected $except = [
        'logout',
        'files/*',
        'file/*',
        'user/profile-information',
        'user/password',
        'user/two-factor-authentication',
        'user/two-factor-qr-code',
        'user/two-factor-recovery-codes',
        'user/two-factor-authentication',
        'user/profile-information',
        'user/profile-information',
        'user/profile-information',
        'user/profile-information',
    ];

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if (Auth::check() && Auth::user()->hasRole('member') && empty(Auth::user()->member)) {
            session()->flash('error', 'No member account found');
            Auth::logout();
            return redirect()->route('login')->with('error', 'No member account found');
        }
        if (Auth::check() && Auth::user()->hasRole('member') && !$request->is('portal/*') && !$this->inExceptArray($request)) {
            return redirect()->route('portal.dashboard');
        }
        return $next($request);
    }

    protected function inExceptArray($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
