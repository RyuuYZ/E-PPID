<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChangeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->must_change_password) {
            $routeName = $request->route()->getName();
            
            if ($routeName !== 'admin.force-change-password' && 
                $routeName !== 'admin.process-force-change-password' && 
                $routeName !== 'admin.logout') {
                return redirect()->route('admin.force-change-password');
            }
        }

        return $next($request);
    }
}
