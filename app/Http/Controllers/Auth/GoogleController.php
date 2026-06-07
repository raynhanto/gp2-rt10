<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable) {
            return redirect('/login?error=cancelled');
        }

        $user = User::upsertFromGoogle([
            'google_id'  => $googleUser->getId(),
            'email'      => $googleUser->getEmail(),
            'avatar_url' => $googleUser->getAvatar(),
        ]);

        Auth::login($user, remember: true);

        if (!$user->profil_lengkap) {
            return redirect('/onboarding');
        }

        return redirect($user->isAdmin() ? '/admin' : '/dashboard');
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    }
}
