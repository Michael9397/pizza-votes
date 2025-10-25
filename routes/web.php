<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

// Public routes
Volt::route('/', 'pages.index')->name('home');

// Dev login route (development only)
if (app()->isLocal()) {
    Route::get('/dev-login', function () {
        $user = \App\Models\User::where('email', 'admin@example.com')->first();
        if ($user) {
            auth()->login($user);
            return redirect()->route('dashboard');
        }
        return redirect()->route('login')->with('error', 'Dev user not found');
    })->name('dev-login');
}

// Voting routes (accessible to everyone - both auth and unauth)
// Using implicit route model binding with ID (legacy route)
Volt::route('restaurants/{restaurant}/vote', 'restaurants.vote')
    ->middleware([])
    ->name('restaurants.vote');

// Public voting link using slug
Volt::route('vote/{slug}', 'restaurants.vote-by-slug')
    ->middleware([])
    ->name('restaurants.vote-by-slug');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    Volt::route('dashboard', 'dashboard')->name('dashboard');
    Volt::route('restaurants', 'restaurants.list')->name('restaurants.index');
    Volt::route('restaurants/create', 'restaurants.create')->name('restaurants.create');
    Volt::route('restaurants/{restaurant}', 'restaurants.show')->name('restaurants.show');
    Volt::route('restaurants/{restaurant}/edit', 'restaurants.edit')->name('restaurants.edit');

    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
