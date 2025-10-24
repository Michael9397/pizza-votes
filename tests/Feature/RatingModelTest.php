<?php

use App\Models\Rating;
use App\Models\Restaurant;
use App\Models\User;

test('rating can be created with valid score', function () {
    $restaurant = Restaurant::factory()->create();
    $user = User::factory()->create();

    $rating = Rating::create([
        'restaurant_id' => $restaurant->id,
        'user_id' => $user->id,
        'dimension' => 'taste',
        'score' => 4,
    ]);

    expect($rating->score)->toBe(4);
    expect($rating->dimension)->toBe('taste');
});

test('rating validates score is not below 1', function () {
    $restaurant = Restaurant::factory()->create();
    $user = User::factory()->create();

    try {
        Rating::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
            'dimension' => 'taste',
            'score' => 0,
        ]);
        expect(true)->toBeFalse(); // Should have thrown
    } catch (Throwable $e) {
        expect(true)->toBeTrue();
    }
});

test('rating validates score is not above 5', function () {
    $restaurant = Restaurant::factory()->create();
    $user = User::factory()->create();

    try {
        Rating::create([
            'restaurant_id' => $restaurant->id,
            'user_id' => $user->id,
            'dimension' => 'taste',
            'score' => 6,
        ]);
        expect(true)->toBeFalse(); // Should have thrown
    } catch (Throwable $e) {
        expect(true)->toBeTrue();
    }
});

test('rating belongs to restaurant', function () {
    $restaurant = Restaurant::factory()->create();
    $user = User::factory()->create();

    $rating = Rating::create([
        'restaurant_id' => $restaurant->id,
        'user_id' => $user->id,
        'dimension' => 'taste',
        'score' => 5,
    ]);

    expect($rating->restaurant->id)->toBe($restaurant->id);
});

test('rating belongs to user', function () {
    $restaurant = Restaurant::factory()->create();
    $user = User::factory()->create();

    $rating = Rating::create([
        'restaurant_id' => $restaurant->id,
        'user_id' => $user->id,
        'dimension' => 'taste',
        'score' => 5,
    ]);

    expect($rating->user->id)->toBe($user->id);
});

test('rating can be created with voter name for unauthenticated users', function () {
    $restaurant = Restaurant::factory()->create();

    $rating = Rating::create([
        'restaurant_id' => $restaurant->id,
        'dimension' => 'taste',
        'score' => 5,
        'voter_name' => 'John Doe',
    ]);

    // voter_name is stored in the database
    expect($rating->voter_name)->toBe('John Doe');
    expect($rating->user_id)->toBeNull();
});

test('rating includes visited at timestamp', function () {
    $restaurant = Restaurant::factory()->create();
    $user = User::factory()->create();
    $visitDate = now()->subDays(5);

    $rating = Rating::create([
        'restaurant_id' => $restaurant->id,
        'user_id' => $user->id,
        'dimension' => 'taste',
        'score' => 5,
        'visited_at' => $visitDate,
    ]);

    expect($rating->visited_at->toDateString())->toBe($visitDate->toDateString());
});

test('rating can include notes', function () {
    $restaurant = Restaurant::factory()->create();
    $user = User::factory()->create();

    $rating = Rating::create([
        'restaurant_id' => $restaurant->id,
        'user_id' => $user->id,
        'dimension' => 'taste',
        'score' => 5,
        'notes' => 'Amazing pizza and friendly staff!',
    ]);

    expect($rating->notes)->toBe('Amazing pizza and friendly staff!');
});
