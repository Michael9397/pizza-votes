<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Volt\Volt;

class VoltServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Mount Volt components from the livewire directory
        // This tells Volt where to look for .php component files
        Volt::mount(resource_path('views/livewire'));

        // Let Volt's default resolver handle .blade.php files
        // (No custom resolver needed anymore since we use .blade.php extension)
    }
}
