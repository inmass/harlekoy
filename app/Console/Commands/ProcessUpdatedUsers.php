<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ProcessUpdatedUsers extends Command
{
    const BATCH_SIZE = 1000;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-updated-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cron job to process updated users that have been queued up';

    protected $requestBody;

    public function __construct()
    {
        parent::__construct();
        $this->requestBody = ['batches' => [['subscribers' => []]]];
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $key = UserService::CACHE_KEY;
        $users = Cache::get($key, collect())->take(self::BATCH_SIZE);
        // dd($users);

        if ($users->isEmpty()) {
            $this->info('No users to process');
            return;
        }

        $this->info(sprintf('Processing %d users', $users->count()));

        $this->processUsers($users);
        // $this->sendBatchToApi();
        $this->removeProcessedUsersFromCache($key, $users);
    }

    protected function processUsers(Collection $users)
    {
        $users->each(function (User $user) {
            $this->info('Processing user ' . $user->id);

            // $this->requestBody['batches'][0]['subscribers'][] = [
            //     'email' => $user->email,
            //     'first_name' => $user->first_name,
            //     'last_name' => $user->last_name,
            //     'time_zone' => $user->timezone,
            // ];

            // normally we would add user data to the request body, but now we'll just log it
            logger()->channel('user_updates')->info(
                sprintf('[%d] email: %s, firstname: %s, timezone: %s', $user->id, $user->email, $user->first_name, $user->timezone)
            );
        });
    }

    protected function sendBatchToApi()
    {
        // this is where we would send the actual api call
        dump($this->requestBody);
    }

    protected function removeProcessedUsersFromCache($key, Collection $processedUsers)
    {
        $remainingUsers = Cache::get($key, collect())->filter(function ($user) use ($processedUsers) {
            return !$processedUsers->contains('id', $user->id);
        });

        Cache::forever($key, $remainingUsers);
    }
}
