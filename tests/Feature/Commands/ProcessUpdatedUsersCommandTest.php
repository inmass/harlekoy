<?php

use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

it('processes correct number of users', function () {
    $users = User::factory()->count(1500)->create();
    // $users->each(fn ($user) => UserService::queueUserForProcessing($user));
    $users->each(function ($user) {
        UserService::queueUserForProcessing($user);
    });

    $this->artisan('app:process-updated-users')
         ->assertExitCode(0);

    expect(Cache::get(UserService::CACHE_KEY))->toHaveCount(500);
});

it('removes processed users from cache', function () {
    $users = User::factory()->count(5)->create();
    $users->each(fn ($user) => UserService::queueUserForProcessing($user));

    $this->artisan('app:process-updated-users');

    expect(Cache::get(UserService::CACHE_KEY))->toBeEmpty();
});

it('handles empty cache', function () {
    $this->artisan('app:process-updated-users')
         ->expectsOutput('No users to process')
         ->assertExitCode(0);
});
