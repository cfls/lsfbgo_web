<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $this->get('/table-au-de-bord')->assertRedirect('/login');
});

test('authenticated users can visit the dashboard', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get('/table-au-de-bord')->assertStatus(200);
});
