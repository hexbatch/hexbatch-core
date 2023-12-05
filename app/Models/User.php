<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Helpers\Utilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property string name
 * @property string username
 * @property string password
 * @property string email
 * @property string email_verified_at
 * @property string remember_token
 * @property string created_at
 * @property string updated_at
 * @property string two_factor_secret
 * @property string two_factor_recovery_codes
 * @property string two_factor_confirmed_at
 * @property int element_type_id
 * @property int element_id
 * @property int user_group_id
 * @property string ref_uuid
 *
 * @property Element user_element
 *
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function user_element() : BelongsTo {
        return $this->belongsTo('App\Models\Element','element_id');
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
        if ($field) {
            return $this->where($field, $value)->firstOrFail();
        } else {
            if (Utilities::is_uuid($value)) {
                //the ref
                $what =  $this->where('ref_uuid',$value)->firstOrFail();
                if ($what) {return $what;}
                return static::getUserByTokenRef($value,true);

            } else {
                //the name
                return $this->where('username',$value)->firstOrFail();
            }
        }

    }

    public static function getUserByTokenRef(string $token_ref, bool $fail = false) : ?User {
        $builder =  User::select('users.*')
            ->join('elements',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use($token_ref) {
                    $join
                        ->on('elements.id','=','users.element_id')
                        ->where('elements.ref_uuid',$token_ref);
                }
            );
        if ($fail) {
            return $builder->firstOrFail();
        }

       return  $builder->first();
    }
}
