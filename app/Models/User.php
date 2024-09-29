<?php

namespace App\Models;

use App\Actions\Fortify\CreateNewUser;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchPermissionException;
use App\Exceptions\RefCodes;
use App\Helpers\UserGroups\GroupGathering;
use App\Helpers\Utilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\HasApiTokens;

//todo add private and public types and put into the user home set

//todo when the user home set is created from the user type element, its put into the Standard set, all_users

//todo create user_base_attribute
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
 * @property int server_id
 * @property string ref_uuid
 *
 * @property Element user_element
 * @property UserGroup user_group
 * @property ElementType user_type
 *
 */
class User extends Authenticatable
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

    const SYSTEM_NAME = 'system';
    const SYSTEM_UUID = '2e3bfcdc-ac5b-4229-8919-b5a9a67f7701';

    public function user_element() : BelongsTo {
        return $this->belongsTo('App\Models\Element','element_id');
    }

    public function user_type() : BelongsTo {
        return $this->belongsTo('App\Models\ElementType','element_type_id');
    }

    public function user_group() : BelongsTo {
        return $this->belongsTo('App\Models\UserGroup','user_group_id');
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
                    if (!$ret) {
                        $ret = static::getUserByTokenRef($value);
                    }

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


    public function initUser() {


        if (!$this->user_group_id) {
            $group =  GroupGathering::SetupNewGroup($this->username);
            $this->user_group_id = $group->id;
            $this->save();
        }

    }

    protected static ?User $system_user = null;
    public static function getOrCreateSystemUser(bool &$b_new = false) : User {
        if (static::$system_user) {return static::$system_user;}
        $user = User::where('ref_uuid',User::SYSTEM_UUID)->first();
        if ($user) {
            return static::$system_user = $user;
        }
        $b_new = true;
        $pw = config('hbc.system_user_pw');
        if (!$pw) {
            throw new \LogicException("System user pw is not set in .evn");
        }
        try {

            $user = (new CreateNewUser)->create([
                "username" => User::SYSTEM_NAME,
                "password" => $pw,
                "password_confirmation" => $pw
            ]);
            $user->ref_uuid =  User::SYSTEM_UUID;
            $user->save();
            $user->refresh();
            return static::$system_user = $user;
        } catch (ValidationException $e) {
            throw new \LogicException("Cannot create system user because ".$e->getMessage());
        }
    }

    public static function buildUser(
        ?int $id = null )
    : Builder
    {

        $build =  User::select('users.*')
            ->selectRaw(" extract(epoch from  users.created_at) as created_at_ts,  extract(epoch from  users.updated_at) as updated_at_ts")

            /** @uses User::user_element(),User::user_type(),User::user_group() */
            ->with('user_element','user_type','user_group')


        ;

        if ($id) {
            $build->where('users.id',$id);
        }


        return $build;
    }


    public function inAdminGroup(int $user_id) : bool {
        $this->initUser();
        if ($this->id === $user_id) {return true;}
        return !!$this->user_group->isAdmin($user_id);
    }

    public function checkAdminGroup(int $user_id) : void {

        if (!$this->inAdminGroup($user_id)) {
            throw new HexbatchPermissionException(__("msg.user_not_priv"),
                \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN,
                RefCodes::USER_NOT_PRIV);
        }
    }

    public function getName() : string {
        return $this->username;
    }
}
