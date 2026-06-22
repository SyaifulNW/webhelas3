<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $userRole = strtolower(Auth::user()->role);
        $allowedRoles = array_map('strtolower', $roles);

        // Check if user has any of the allowed roles (exact match)
        if (in_array($userRole, $allowedRoles)) {
            return $next($request);
        }

        // Check prefix patterns: jika allowed role diakhiri '_' (misal 'chapter_'),
        // izinkan semua user role yang dimulai dengan prefix tersebut
        foreach ($allowedRoles as $allowed) {
            if (str_ends_with($allowed, '_') && str_starts_with($userRole, $allowed)) {
                return $next($request);
            }
        }

        // Special case: allow cs_supervisor to access administrator routes
        if (Auth::user()->hasHakAkses('cs_supervisor') && in_array('administrator', $allowedRoles)) {
            return $next($request);
        }

        return redirect('/home')->with('error', 'Akses ditolak.');
    }

}
