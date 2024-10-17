<?php

namespace App\Services;

use App\Models\User;
use App\Jobs\ProcessUserUpdates;
use Illuminate\Support\Facades\Cache;

class UserService
{
    const CACHE_KEY = 'users_to_update';
    const CACHE_TTL = 30;

    public static function queueUserForProcessing(User $user)
    {
        //
    }
}
