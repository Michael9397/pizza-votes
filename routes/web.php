<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

// Public routes
Volt::route('/', 'pages.index')->name('home');

// Voting route (accessible to everyone - both auth and unauth)
// Using implicit route model binding
Volt::route('restaurants/{restaurant}/vote', 'restaurants.vote')
    ->middleware([])
    ->name('restaurants.vote');

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
