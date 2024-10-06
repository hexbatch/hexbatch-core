<?php

namespace App\System\Resources\Users;


use App\Actions\Fortify\CreateNewUser;
use App\Exceptions\HexbatchInitException;
use App\Models\User;

abstract class BaseSystemUser implements ISystemUser
{
    protected ?User $user;

    public function makeUser() :User
   {
       try {
           $user = (new CreateNewUser)->create([
               "username" => $this->getUserName(),
               "password" => $this->getUserPassword(),
               "password_confirmation" => $this->getUserPassword()
           ]);
           $user->ref_uuid = $this->getUserUuid();
           $user->save();
           $user->refresh();
           return $user;
       } catch (\Exception $e) {
            throw new HexbatchInitException($e->getMessage(),$e->getCode(),null,$e);
       }
   }

    public function getUserObject() : User {
        if ($this->user) {return $this->user;}
        $this->user = $this->makeUser();
        return $this->user;
    }



}
