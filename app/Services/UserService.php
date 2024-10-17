<?php

namespace App\Services;

use App\Models\User;
use App\Jobs\ProcessUserUpdates;
use Illuminate\Support\Facades\Cache;

class UserService
{
    const CACHE_KEY = 'users_to_update';

    public static function queueUserForProcessing(User $user)
    {
        $key = self::CACHE_KEY;
        self::createCacheIfNotExists($key);
        
        $users = Cache::get($key);
        // we want to make sure we send the latest data to the queue
        $users = $users->keyBy('id');
        $users[$user->id] = $user;
        
        Cache::put($key, $users->values());
    }

    private static function createCacheIfNotExists(string $key)
    {
        if (!Cache::has($key)) {
            Cache::forever($key, collect());
        }
    }
}
