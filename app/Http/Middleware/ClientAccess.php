<?php

namespace App\Http\Middleware;

use App\User;
use Auth;
use Closure;

class ClientAccess
{
    /**
     * Доступ только обычным пользователям.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->isUser()) {
            return abort(403);
        }
        return $next($request);
    }
}
