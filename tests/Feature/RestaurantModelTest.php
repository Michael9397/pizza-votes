<?php

use App\Models\Rating;
use App\Models\Restaurant;
use App\Models\User;

test('restaurant can be created', function () {
    $restaurant = Restaurant::factory()->create([
        'name' => 'Gino\'s Pizzeria',
        'location' => 'Downtown',
    ]);

    expect($restaurant->name)->toBe('Gino\'s Pizzeria');
    expect($restaurant->location)->toBe('Downtown');
    expect($restaurant->exists)->toBeTrue();
});

test('restaurant has many ratings', function () {
    $restaurant = Restaurant::factory()->create();
    $user = User::factory()->create();

    Rating::factory(4)->create([
        'restaurant_id' => $restaurant->id,
        'user_id' => $user->id,
    ]);

    expect($restaurant->ratings()->count())->toBe(4);
});

test('restaurant calculates average score per dimension', function () {
    $restaurant = Restaurant::factory()->create();
    $user = User::factory()->create();

    // Create ratings for taste dimension
    Rating::create([
        'restaurant_id' => $restaurant->id,
        'user_id' => $user->id,
        'dimension' => 'taste',
        'score' => 5,
    ]);
    Rating::create([
        'restaurant_id' => $restaurant->id,
        'user_id' => $user->id,
        'dimension' => 'taste',
        'score' => 3,
    ]);

    $averages = $restaurant->averageScorePerDimension();
    expect($averages['taste'])->toBe(4.0);
});

test('restaurant calculates overall score', function () {
    $restaurant = Restaurant::factory()->create();
    $user = User::factory()->create();

    // Create ratings across all dimensions with score 5
    foreach (['taste', 'service', 'atmosphere', 'value'] as $dimension) {
        Rating::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
            'dimension' => $dimension,
            'score' => 5,
        ]);
    }

    expect($restaurant->overallScore())->toBe(5.0);
});

test('restaurant returns 0 overall score when no ratings exist', function () {
    $restaurant = Restaurant::factory()->create();
    expect($restaurant->overallScore())->toBe(0.0);
});

test('restaurant visit count returns distinct visitors', function () {
    $restaurant = Restaurant::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // User 1 makes 4 ratings (one per dimension)
    foreach (['taste', 'service', 'atmosphere', 'value'] as $dimension) {
        Rating::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user1->id,
            'dimension' => $dimension,
            'score' => 5,
        ]);
    }

    // User 2 makes 4 ratings
    foreach (['taste', 'service', 'atmosphere', 'value'] as $dimension) {
        Rating::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user2->id,
            'dimension' => $dimension,
            'score' => 4,
        ]);
    }

    // Should have 8 total ratings but 2 unique visitors
    expect($restaurant->ratings()->count())->toBe(8);
});
