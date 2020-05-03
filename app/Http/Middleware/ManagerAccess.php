<?php

namespace App\Http\Middleware;

use App\User;
use Auth;
use Closure;

class ManagerAccess
{
    /**
     * Доступ только менеджерам.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->isManager()) {
            return abort(403);
        }
        return $next($request);
    }
}
