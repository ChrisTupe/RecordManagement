<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:make-admin {email : The email of the user to make admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a user an admin';

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

        if ($user->isAdmin()) {
            $this->info("User '{$user->name}' is already an admin.");
            return;
        }

        $user->update(['role' => User::ROLE_ADMIN]);
        $this->info("User '{$user->name}' ({$user->email}) has been made an admin.");
    }
}
