<?php

namespace App\Console\Commands;

use App\Exceptions\HexbatchNotFound;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateUserAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hbc:create_user_auth {username_or_id} {--S|seconds=} {--N|token_name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'creates a new token for the user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user_id_or_name = $this->argument('username_or_id');
        try {
            $user = (new User)->resolveRouteBinding($user_id_or_name);
        } catch (HexbatchNotFound $e) {
            $this->error($e->getMessage());
            return;
        }
        $expires = null;
        $seconds = null;
        if ($this->hasOption('seconds')) {
            $seconds = (int)$this->option('seconds');
        }


        if ($seconds && $seconds > 0) {
            $expires = Carbon::now()->addSeconds($seconds);
        }

        $token_name = $this->option('token_name');
        if (empty($token_name)) {$token_name = '';}

        $token = $user->createToken($token_name,['*'],$expires);
        $this->line($token->plainTextToken);
    }
}
