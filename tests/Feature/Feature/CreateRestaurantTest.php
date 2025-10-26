<?php

use App\Models\User;

test('authenticated user can access create restaurant page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('restaurants.create'));

    $response->assertStatus(200);
    $response->assertSee('Add New Restaurant');
});

test('unauthenticated user cannot access create restaurant page', function () {
    $response = $this->get(route('restaurants.create'));

    $response->assertRedirect(route('login'));
});

test('create restaurant page contains form fields', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('restaurants.create'));

    $response->assertSee('Restaurant Name');
    $response->assertSee('Location');
    $response->assertSee('Notes');
    $response->assertSee('Create Restaurant');
});
