<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UpdateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $input = $this->ask('Provide the user ID to update: ');
        $user = User::find($input);
        if (!$user) {
            $this->error('User not found');
            return;
        }

        $this->info('User old values: ' . $user->first_name . ' ' . $user->last_name . ' ' . $user->timezone);

        $user->update([
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'timezone' => fake()->randomElement(['CET', 'CST', 'GMT+1']),
        ]);

        $this->info('User updated successfully');
        $this->info('User new values: ' . $user->first_name . ' ' . $user->last_name . ' ' . $user->timezone);
    }
}
