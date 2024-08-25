<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Routing\Route;
use \Illuminate\Session\Middleware\StartSession as BaseStartSession;
use Illuminate\Support\Facades\Schema;

class StartSession extends BaseStartSession
{

    public function handle($request, Closure $next)
    {
        //check if we are using database and if the schema has been created yet
        try {
            if (config('session.driver') === 'database' && !Schema::hasTable('sessions')) {
                config(['session.driver' => 'file']);
            }
        }catch (Exception $exception){
            config(['session.driver' => 'file']);
        }

        if (!$this->sessionConfigured()) {
            return $next($request);
        }

        $session = $this->getSession($request);

        if ($this->manager->shouldBlock() ||
            ($request->route() instanceof Route && $request->route()->locksFor())) {
            return $this->handleRequestWhileBlocking($request, $session, $next);
        }

        return $this->handleStatefulRequest($request, $session, $next);
    }
}
