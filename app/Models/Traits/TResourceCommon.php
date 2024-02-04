<?php

namespace App\Models\Traits;

use App\Exceptions\HexbatchNameConflictException;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\User;
use App\Rules\ResourceNameReq;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait TResourceCommon
{

    public function isInUse() : bool {
        return false;
    }

    public function checkIsInUse() : void {
        if (!$this->isInUse()) {return;}
        throw new HexbatchNotPossibleException(__("msg.bounds_in_use_cannot_change"),
            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
            RefCodes::BOUND_CANNOT_EDIT);
    }

    /**
     * @param string $name
     * @param User $owner
     * @return void
     * @throws ValidationException
     */
    public function setName(string $name, User $owner) {
        Validator::make(['bound_name'=>$name], [
            'bound_name'=>['required','string','max:128',new ResourceNameReq],
        ])->validate();

        $conflict =  static::where('user_id', $owner->id)->where('bound_name',$name)->first();
        if ($conflict) {
            throw new HexbatchNameConflictException(__("msg.unique_resource_name_per_user",['resource_name'=>$name]),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::RESOURCE_NAME_UNIQUE_PER_USER);
        }

        $this->bound_name = $name;
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $ret = null;
        try {
            if ($field) {
                $ret = $this->where($field, $value)->first();
            } else {
                if (Utilities::is_uuid($value)) {
                    //the ref
                    $ret = $this->where('ref_uuid', $value)->first();
                } else {
                    if (is_string($value)) {
                        //the name, but scope to the user id of the owner
                        //if this user is not the owner, then the group owner id can be scoped
                        $parts = explode('.', $value);
                        if (count($parts) === 1) {
                            //must be owned by the user
                            $user = auth()->user();
                            $ret = $this->where('user_id', $user?->id)->where('bound_name', $value)->first();
                        } else {
                            $owner = $parts[0];
                            $maybe_name = $parts[1];
                            $owner = (new User)->resolveRouteBinding($owner);
                            $ret = $this->where('user_id', $owner?->id)->where('bound_name', $maybe_name)->first();
                        }
                    }
                }
            }
        } finally {
            if (empty($ret)) {
                throw new HexbatchNotFound(
                    __('msg.bound_not_found',['ref'=>$value]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                    RefCodes::BOUND_NOT_FOUND
                );
            }
        }
        return $ret;

    }
}
