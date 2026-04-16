<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    return view('landing');
})->name('home');

Route::get('/auth/google', function () {
    return Socialite::driver('google')
        ->scopes(['openid', 'profile', 'email'])
        ->redirect();
})->name('auth.google');

Route::get('/auth/google/callback', function (Request $request) {
    try {
        $googleUser = Socialite::driver('google')->user();
    } catch (\Throwable $e) {
        return redirect('/')->withErrors(['google' => 'sign-in failed: ' . $e->getMessage()]);
    }

    session([
        'user' => [
            'id' => $googleUser->getId(),
            'email' => $googleUser->getEmail(),
            'name' => $googleUser->getName(),
            'avatar' => $googleUser->getAvatar(),
        ],
    ]);

    try {
        if (Schema::hasTable('signins')) {
            DB::table('signins')->insert([
                'google_id' => $googleUser->getId(),
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
                'avatar' => $googleUser->getAvatar(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    } catch (\Throwable) {
        // sign-in logging is best-effort; never block auth
    }

    return redirect('/wizard');
})->name('auth.google.callback');

Route::post('/auth/signout', function (Request $request) {
    $request->session()->forget('user');
    return redirect('/');
})->name('auth.signout');

Route::get('/wizard', function () {
    if (!session('user')) {
        return redirect('/auth/google');
    }
    return view('wizard');
})->name('wizard');
