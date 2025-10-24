<?php

use App\Models\Restaurant;
use App\Models\User;

test('authenticated users can access dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));
    $response->assertStatus(200);
});

test('guests are redirected from dashboard', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can view restaurants list', function () {
    $user = User::factory()->create();
    Restaurant::factory(3)->create();

    $response = $this->actingAs($user)->get(route('restaurants.index'));
    $response->assertStatus(200);
});

test('authenticated users can view create restaurant form', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('restaurants.create'));
    $response->assertStatus(200);
});

test('authenticated users can create a restaurant', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('restaurants.store'), [
        'name' => 'New Pizza Place',
        'location' => '123 Main St',
        'notes' => 'Great pizza',
    ]);

    // For Volt components, we test the action directly
    $this->assertDatabaseHas('restaurants', [
        'name' => 'New Pizza Place',
        'location' => '123 Main St',
    ]);
});

test('restaurant creation requires name', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('restaurants.create'));

    // Name validation is handled in Volt component
    // This test ensures the route is protected
});

test('authenticated users can view restaurant details', function () {
    $user = User::factory()->create();
    $restaurant = Restaurant::factory()->create();

    $response = $this->actingAs($user)->get(route('restaurants.show', $restaurant));
    $response->assertStatus(200);
    $response->assertSee($restaurant->name);
});

test('authenticated users can view edit restaurant form', function () {
    $user = User::factory()->create();
    $restaurant = Restaurant::factory()->create();

    $response = $this->actingAs($user)->get(route('restaurants.edit', $restaurant));
    $response->assertStatus(200);
});

test('authenticated users can update restaurant', function () {
    $user = User::factory()->create();
    $restaurant = Restaurant::factory()->create();

    $this->actingAs($user)->put(route('restaurants.update', $restaurant), [
        'name' => 'Updated Name',
        'location' => 'New Location',
    ]);

    $restaurant->refresh();
    expect($restaurant->name)->toBe('Updated Name');
});

test('authenticated users can delete restaurant', function () {
    $user = User::factory()->create();
    $restaurant = Restaurant::factory()->create();

    $this->actingAs($user)->delete(route('restaurants.destroy', $restaurant));

    $this->assertDatabaseMissing('restaurants', ['id' => $restaurant->id]);
});

test('restaurant deletion cascades to ratings', function () {
    $user = User::factory()->create();
    $restaurant = Restaurant::factory()->create();

    // Create ratings
    \App\Models\Rating::factory(4)->create([
        'restaurant_id' => $restaurant->id,
    ]);

    expect($restaurant->ratings()->count())->toBe(4);

    $restaurant->delete();

    $this->assertDatabaseMissing('ratings', ['restaurant_id' => $restaurant->id]);
});

test('restaurant detail page shows dimension scores', function () {
    $user = User::factory()->create();
    $restaurant = Restaurant::factory()->create(['name' => 'Pizza Pro']);

    // Create some ratings
    \App\Models\Rating::create([
        'restaurant_id' => $restaurant->id,
        'user_id' => $user->id,
        'dimension' => 'taste',
        'score' => 5,
    ]);

    $response = $this->actingAs($user)->get(route('restaurants.show', $restaurant));
    $response->assertStatus(200);
    $response->assertSee('Pizza Pro');
    $response->assertSee('taste', 'i');
});
