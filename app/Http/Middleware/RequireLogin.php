<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequireLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Login diperlukan'], 401);
            }
            return redirect('/login?redirect=' . urlencode($request->getRequestUri()));
        }

        // Redirect to onboarding if profile is incomplete (skip admins — they may be manually created)
        if (!Auth::user()->profil_lengkap
            && !Auth::user()->isAdmin()
            && !$request->is('onboarding', 'api/user/profile'))
        {
            return redirect('/onboarding');
        }

        return $next($request);
    }
}
