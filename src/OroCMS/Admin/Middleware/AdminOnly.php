<?php
namespace OroCMS\Admin\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()->is('admin')) {
            // Unknown permission? remove session
            if (auth()->check()) {
                auth()->logout();
            }

            return redirect()->route('admin.login.index');
        }

        return $next($request);
    }
}
