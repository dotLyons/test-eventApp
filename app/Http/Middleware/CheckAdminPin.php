<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminPin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si NO existe la variable 'admin_access' en la sesión...
        if (!session()->has('admin_access')) {
            // ... lo mandamos a la pantalla de login
            return redirect()->route('login.pin');
        }

        return $next($request);
    }
}
