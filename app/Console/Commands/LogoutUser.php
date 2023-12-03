<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class LogoutUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hbc:logout_user {username_or_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Logs the user out';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user_id_or_name = $this->argument('username_or_id');
        if (ctype_digit($user_id_or_name)) {
            $user = User::where('id',$user_id_or_name)->first();
        } else {
            $user = User::where('username',$user_id_or_name)->first();
        }
        if (empty($user)) {
            $this->error("Could not find user with $user_id_or_name");
            return;
        }

        $num = $user->tokens()->delete();
        $this->info("Deleted $num token for $user->username");
    }
}
