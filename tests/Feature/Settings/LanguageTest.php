<?php

use App\Models\User;

test('user can update their locale', function () {
    $user = User::factory()->create(['locale' => 'en']);

    $this->actingAs($user)
        ->patch(route('language.update'), ['locale' => 'cs'])
        ->assertRedirect();

    expect($user->fresh()->locale)->toBe('cs');
});

test('locale is required', function () {
    $user = User::factory()->create(['locale' => 'en']);

    $this->actingAs($user)
        ->patch(route('language.update'), ['locale' => ''])
        ->assertSessionHasErrors('locale');
});

test('locale must be a supported value', function () {
    $user = User::factory()->create(['locale' => 'en']);

    $this->actingAs($user)
        ->patch(route('language.update'), ['locale' => 'de'])
        ->assertSessionHasErrors('locale');

    expect($user->fresh()->locale)->toBe('en');
});

test('guest cannot update locale', function () {
    $this->patch(route('language.update'), ['locale' => 'cs'])
        ->assertRedirect(route('login'));
});
