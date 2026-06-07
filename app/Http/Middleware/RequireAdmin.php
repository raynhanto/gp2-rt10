<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequireAdmin
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Login diperlukan'], 401);
            }
            return redirect('/login');
        }

        $user          = Auth::user();
        $allowedRoles  = empty($roles) ? \App\Models\User::ADMIN_ROLES : $roles;

        if (!in_array($user->role, $allowedRoles, true)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Akses tidak diizinkan'], 403);
            }
            abort(403, 'Akses tidak diizinkan.');
        }

        return $next($request);
    }
}
