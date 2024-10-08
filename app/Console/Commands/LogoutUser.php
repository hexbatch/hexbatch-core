<?php

namespace App\Console\Commands;

use App\Exceptions\HexbatchNotFound;
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
        try {
            /** @var User $user */
            $user = (new User)->resolveRouteBinding($user_id_or_name);
        } catch (HexbatchNotFound $e) {
            $this->error($e->getMessage());
            return;
        }

        $num = $user->tokens()->delete();
        $this->info("Deleted $num token for $user->username");
    }
}
