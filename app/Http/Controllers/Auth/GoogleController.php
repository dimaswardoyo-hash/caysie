<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect the user to Google's OAuth consent screen.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')
            ->redirect();
    }
 
    /**
     * Handle the callback from Google after the user authorizes the app.
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::warning('Google login failed: ' . $e->getMessage());
 
            return redirect()->route('login')
                ->with('error', 'Gagal masuk dengan Google. Silakan coba lagi.');
        }
 
        // 1) Sudah pernah login dengan Google sebelumnya
        $user = User::where('google_id', $googleUser->getId())->first();
 
        // 2) Belum pernah pakai Google, tapi email sudah terdaftar (daftar manual) -> tautkan akun
        if (! $user) {
            $user = User::where('email', $googleUser->getEmail())->first();
 
            if ($user) {
                $user->forceFill([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ])->save();
            }
        }
 
        // 3) Belum ada sama sekali -> buat akun baru otomatis
        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName() ?? $googleUser->getNickname(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => Hash::make(Str::random(32)),
                'email_verified_at' => now(),
            ]);
        }
 
        Auth::login($user, remember: true);
 
        request()->session()->regenerate();
 
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
 
        return redirect()->route('user.dashboard');
    }
}
