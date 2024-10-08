<?php

namespace App\Console\Commands;

use App\Exceptions\HexbatchNotFound;
use App\Models\User;
use Illuminate\Console\Command;

class DeleteUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hbc:delete_user {username_or_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes the user if no elements or types they own are in sets of other people';

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
        $user->delete();
        $this->info("Deleted $user->username");
    }
}
