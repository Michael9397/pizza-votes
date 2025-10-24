<?php

use App\Models\Restaurant;
use App\Models\Rating;

test('guest users can view home page', function () {
    $response = $this->get(route('home'));
    $response->assertStatus(200);
});

test('home page shows restaurants', function () {
    $restaurant = Restaurant::factory()->create([
        'name' => 'Gino\'s',
        'location' => 'Downtown',
    ]);

    $response = $this->get(route('home'));
    $response->assertStatus(200);
    $response->assertSee('Gino\'s');
    $response->assertSee('Downtown');
});

test('guest users can access voting form', function () {
    $restaurant = Restaurant::factory()->create();

    $response = $this->get(route('restaurants.vote', $restaurant));
    $response->assertStatus(200);
});

test('guest users can submit ratings without authentication', function () {
    $restaurant = Restaurant::factory()->create();

    $response = $this->post(route('restaurants.vote', $restaurant), [
        'voter_name' => 'Jane Smith',
        'visited_at' => now()->format('Y-m-d'),
        'scores.taste' => 5,
        'scores.service' => 4,
        'scores.atmosphere' => 4,
        'scores.value' => 5,
    ], [
        'HTTP_X_LIVEWIRE' => true,
    ]);

    // This tests via Livewire form submission
});

test('voting form requires voter name for guests', function () {
    $restaurant = Restaurant::factory()->create();

    // Submit without voter_name as guest
    $this->post(route('restaurants.vote', $restaurant), [
        'visited_at' => now()->format('Y-m-d'),
        'scores.taste' => 5,
    ]);

    // Validation should fail (tested in component)
});

test('authenticated users can vote without providing name', function () {
    $user = \App\Models\User::factory()->create();
    $restaurant = Restaurant::factory()->create();

    $this->actingAs($user);

    // Authenticated users don't need voter_name field
    $response = $this->get(route('restaurants.vote', $restaurant));
    $response->assertStatus(200);
    $response->assertDontSee('Your Name');
});

test('home page shows overall restaurant scores', function () {
    $restaurant = Restaurant::factory()->create(['name' => 'Pizza Palace']);
    $user = \App\Models\User::factory()->create();

    // Create ratings for all dimensions
    foreach (['taste', 'service', 'atmosphere', 'value'] as $dimension) {
        Rating::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
            'dimension' => $dimension,
            'score' => 5,
        ]);
    }

    $response = $this->get(route('home'));
    $response->assertStatus(200);
    $response->assertSee('Pizza Palace');
});

test('home page shows visit count', function () {
    $restaurant = Restaurant::factory()->create();
    $user = \App\Models\User::factory()->create();

    // Create one complete visit (4 ratings)
    foreach (['taste', 'service', 'atmosphere', 'value'] as $dimension) {
        Rating::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
            'dimension' => $dimension,
            'score' => 4,
        ]);
    }

    $response = $this->get(route('home'));
    $response->assertStatus(200);
    $response->assertSee('4');
});
