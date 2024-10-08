<?php

namespace App\Console\Commands;

use App\Actions\Fortify\PasswordValidationRules;
use App\Exceptions\HexbatchNotFound;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ChangeUserPw extends Command
{
    use PasswordValidationRules;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hbc:change_user_pw {username_or_id} {new_password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Changes the user password';

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
        $new_password = $this->argument('new_password');

        try {
            Validator::make(['password' => $new_password, 'password_confirmation' => $new_password], [
                'password' => $this->passwordRules(),
            ])->validate();
        } catch (ValidationException $v) {
            $this->error($v->getMessage());
            return;
        }

        $user->forceFill([
            'password' => Hash::make($new_password),
        ])->save();

        $this->info("Changed pw for $user->username");
    }
}
