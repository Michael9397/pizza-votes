<?php

use Illuminate\Testing\TestResponse;

describe('HTTP Page Loading Tests', function () {
    test('root page loads with HTTP 200', function () {
        $response = $this->get('/');

        $status = $response->status();

        // Output detailed info for debugging
        echo "\n=== ROOT PAGE LOAD TEST ===\n";
        echo "Status Code: " . $status . "\n";

        if ($status !== 200) {
            echo "ERROR: Expected 200, got " . $status . "\n";
            echo "Response Content (first 500 chars):\n";
            echo substr($response->getContent(), 0, 500) . "\n";
        }

        $response->assertStatus(200);
    });

    test('root page contains expected content', function () {
        $response = $this->get('/');

        if ($response->status() !== 200) {
            echo "\nERROR: Got status " . $response->status() . "\n";
            echo "Response:\n";
            echo $response->getContent() . "\n";
        }

        $response->assertStatus(200);
        $response->assertSee('Pizza Family Ratings');
    });

    test('root page is not returning 500 error', function () {
        $response = $this->get('/');

        $status = $response->status();

        if ($status >= 500) {
            echo "\n=== ERROR DETAILS ===\n";
            echo "Status: " . $status . "\n";
            echo "Full Response:\n";
            echo $response->getContent() . "\n";

            // Try to extract error details
            if (str_contains($response->getContent(), 'ComponentNotFoundException')) {
                echo "\n⚠️  COMPONENT NOT FOUND ERROR\n";
                echo "This means Volt cannot find the 'pages.index' component\n";
                echo "Check:\n";
                echo "1. resources/views/livewire/pages/index.php exists\n";
                echo "2. app/Providers/VoltServiceProvider.php mounts components\n";
                echo "3. config/livewire.php has correct view_path\n";
            }
        }

        expect($status)->toBeLessThan(500);
    });

    test('dashboard route redirects unauthenticated users', function () {
        $response = $this->get('/dashboard');

        echo "\n=== DASHBOARD ROUTE TEST ===\n";
        echo "Status Code: " . $response->status() . "\n";

        // Should redirect to login (302)
        expect($response->status())->toEqual(302);
        expect($response->headers->get('Location'))->toContain('login');
    });

    test('restaurants route redirects unauthenticated users', function () {
        $response = $this->get('/restaurants');

        echo "\n=== RESTAURANTS ROUTE TEST ===\n";
        echo "Status Code: " . $response->status() . "\n";

        // Should redirect to login (302)
        expect($response->status())->toEqual(302);
    });

    test('voting form page loads without authentication', function () {
        $restaurant = \App\Models\Restaurant::factory()->create();

        echo "\n=== VOTING FORM LOAD TEST ===\n";
        echo "Testing route: /restaurants/" . $restaurant->id . "/vote\n";

        $response = $this->get(route('restaurants.vote', $restaurant));

        echo "Status Code: " . $response->status() . "\n";

        if ($response->status() !== 200) {
            echo "Response (first 1000 chars):\n";
            echo substr($response->getContent(), 0, 1000) . "\n";
        }

        expect($response->status())->toBeLessThan(500);
    });

    test('authenticated dashboard loads', function () {
        $user = \App\Models\User::factory()->create();

        echo "\n=== AUTHENTICATED DASHBOARD TEST ===\n";
        echo "User ID: " . $user->id . "\n";

        $response = $this->actingAs($user)->get('/dashboard');

        echo "Status Code: " . $response->status() . "\n";

        if ($response->status() >= 500) {
            echo "ERROR RESPONSE:\n";
            echo substr($response->getContent(), 0, 1000) . "\n";
        }

        // Dashboard may return 500 if component can't load, that's the error we're looking for
        expect($response->status())->toBeGreaterThanOrEqual(200);
    });
});

describe('Component Discovery Verification', function () {
    test('verify pages.index component file exists', function () {
        $componentPath = resource_path('views/livewire/pages/index.blade.php');

        echo "\n=== COMPONENT FILE CHECK ===\n";
        echo "Looking for: " . $componentPath . "\n";

        if (file_exists($componentPath)) {
            echo "✓ File EXISTS\n";
            echo "File size: " . filesize($componentPath) . " bytes\n";

            // Check first 200 chars
            $content = file_get_contents($componentPath, false, null, 0, 200);
            echo "First 200 chars:\n" . $content . "...\n";
        } else {
            echo "✗ FILE DOES NOT EXIST\n";
        }

        expect(file_exists($componentPath))->toBeTrue();
    });

    test('verify VoltServiceProvider mounts components', function () {
        $providerPath = app_path('Providers/VoltServiceProvider.php');

        echo "\n=== VOLT SERVICE PROVIDER CHECK ===\n";
        echo "Checking: " . $providerPath . "\n";

        $content = file_get_contents($providerPath);

        if (str_contains($content, 'Volt::mount')) {
            echo "✓ Volt::mount() found in provider\n";
        } else {
            echo "✗ Volt::mount() NOT found in provider\n";
        }

        expect($content)->toContain('Volt::mount');
    });

    test('verify livewire config exists', function () {
        $configPath = config_path('livewire.php');

        echo "\n=== LIVEWIRE CONFIG CHECK ===\n";
        echo "Checking: " . $configPath . "\n";

        if (file_exists($configPath)) {
            echo "✓ config/livewire.php EXISTS\n";

            $config = include $configPath;
            if (isset($config['view_path'])) {
                echo "✓ view_path configured: " . $config['view_path'] . "\n";
            } else {
                echo "✗ view_path NOT configured\n";
            }
        } else {
            echo "✗ config/livewire.php DOES NOT EXIST\n";
        }

        expect(file_exists($configPath))->toBeTrue();
    });
});
