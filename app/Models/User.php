<?php

namespace App\Models;

use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Sys\Res\ISystemModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int default_namespace_id
 * @property bool is_system
 * @property string ref_uuid
 * @property string name
 * @property string username
 * @property string password
 * @property string email
 * @property string email_verified_at
 * @property string remember_token
 * @property string two_factor_secret
 * @property string two_factor_recovery_codes
 * @property string two_factor_confirmed_at
 *
 * @property string created_at
 * @property int created_at_ts
 * @property string updated_at
 *
 * @property UserNamespace default_namespace
 * @property UserNamespace[] my_namespaces
 *
 */
class User extends Authenticatable implements ISystemModel
{
    use HasApiTokens, HasFactory, Notifiable;
    public $timestamps = false;
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
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['default_namespace'];

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


    public function my_namespaces() : HasMany {
        return $this->hasMany(UserNamespace::class,'namespace_user_id');
    }

    public function default_namespace() : BelongsTo {
        return $this->belongsTo(UserNamespace::class,'default_namespace_id');
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
                    //the name
                    $ret = $this->where('username', $value)->first();
                }
            }
        } finally {
            if (empty($ret)) {
                throw new HexbatchNotFound(
                    __('msg.user_not_found',['ref'=>$value]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                    RefCodes::USER_NOT_FOUND
                );
            } else {
                $out = User::buildUser($ret->id)->first();
            }
        }
        return $out;

    }

    public static function getUser(
        ?int $id = null )
    : User
    {
        $ret = static::buildUser(id:$id)->first();

        if (!$ret) {
            $arg_types = [];
            $arg_vals = [];
            if ($id) { $arg_types[] = 'id'; $arg_vals[] = $id;}
            $arg_val = implode('|',$arg_vals);
            $arg_type = implode('|',$arg_types);
            throw new \LogicException("Could not find user via $arg_type : $arg_val");
        }
        return $ret;
    }

    public static function buildUser(
        ?int $id = null )
    : Builder
    {

        $build =  User::select('users.*')
            ->selectRaw(" extract(epoch from  users.created_at) as created_at_ts,  extract(epoch from  users.updated_at) as updated_at_ts")

            /** @uses User::my_namespaces(),User::default_namespace() */
            ->with('my_namespaces','default_namespace')


        ;

        if ($id) {
            $build->where('users.id',$id);
        }


        return $build;
    }

    public function getName() : string {
        return $this->username;
    }

    public function getUuid(): string {
        return $this->ref_uuid;
    }


}
