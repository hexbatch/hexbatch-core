<?php

namespace App\Models;

use App\Helpers\Utilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property string ref_uuid
 * @property int user_id
 * @property string group_name
 * @property string created_at
 * @property string updated_at
 *
 */
class UserGroup extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'is_retired',
        'group_name',
        'user_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        if ($field) {
            return $this->where($field, $value)->firstOrFail();
        } else {
            if (ctype_digit($value)) {
                return $this->where('id',$value)->firstOrFail();
            } elseif (Utilities::is_uuid($value)) {
                //the ref
                return $this->where('ref_uuid',$value)->firstOrFail();
            } else {
                //the name, but scope to the user id logged in
                /**
                 * @var User $user
                 */
                $user = Auth::getUser();
                return $this->where('user_id', $user?->id)->where('group_name',$value)->firstOrFail();
            }
        }

    }
}
