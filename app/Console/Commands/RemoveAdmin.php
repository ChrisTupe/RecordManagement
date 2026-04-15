<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class RemoveAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:remove-admin {email : The email of the admin user to demote}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove admin privileges from a user';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return;
        }

        if (!$user->isAdmin()) {
            $this->info("User '{$user->name}' is not an admin.");
            return;
        }

        $user->update(['role' => User::ROLE_USER]);
        $this->info("Admin privileges have been removed from '{$user->name}' ({$user->email}).");
    }
}
