<?php

use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Cache;

test('queue user for processing creates cache if not exists', function () {
    $user = User::factory()->create();
    
    UserService::queueUserForProcessing($user);

    expect(Cache::has(UserService::CACHE_KEY))->toBeTrue();
});

test('adds user to cache when queueing for processing', function () {
    $user = User::factory()->create();
    
    UserService::queueUserForProcessing($user);

    $cachedUsers = Cache::get(UserService::CACHE_KEY);
    expect($cachedUsers)->toHaveCount(1);
    expect($cachedUsers->first()->id)->toBe($user->id);
});

test('updates existing user in cache when queueing for processing', function () {
    $user = User::factory()->create();
    
    UserService::queueUserForProcessing($user);
    
    $user->first_name = 'UpdatedName';
    UserService::queueUserForProcessing($user);

    $cachedUsers = Cache::get(UserService::CACHE_KEY);
    expect($cachedUsers)->toHaveCount(1);
    expect($cachedUsers->first()->first_name)->toBe('UpdatedName');
});
