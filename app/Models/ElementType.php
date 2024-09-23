<?php

namespace App\Models;

use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int editing_user_group_id
 * @property int inheriting_user_group_id
 * @property int new_elements_user_group_id
 * @property string ref_uuid
 * @property int user_id
 * @property boolean is_retired
 * @property boolean is_system
 * @property boolean is_final
 * @property string type_name
 * @property string created_at
 * @property string updated_at
 *
 * @property User type_owner
 * @property UserGroup editing_group
 * @property UserGroup inheriting_group
 * @property UserGroup new_elements_group
 *
 */
class ElementType extends Model
{

    protected $table = 'element_types';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'is_retired',
        'type_name',
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

    public function type_owner() : BelongsTo {
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function editing_group() : BelongsTo {
        return $this->belongsTo('App\Models\UserGroup','editing_user_group_id');
    }

    public function inheriting_group() : BelongsTo {
        return $this->belongsTo('App\Models\UserGroup','inheriting_user_group_id');
    }

    public function new_elements_group() : BelongsTo {
        return $this->belongsTo('App\Models\UserGroup','new_elements_user_group_id');
    }

    public function type_attributes() : HasMany {
        return $this->hasMany('App\Models\Attribute','owner_element_type_id','id');
    }

    public static function buildElementType(
        ?int $id = null,
        ?int $user_id = null
    )
    : Builder
    {

        $build = ElementType::select('element_types.*')
            ->selectRaw(" extract(epoch from  element_types.created_at) as created_at_ts,  extract(epoch from  element_types.updated_at) as updated_at_ts")

            /** @uses ElementType::type_owner(),ElementType::editing_group(),ElementType::inheriting_group(),ElementType::new_elements_group(),ElementType::type_attributes() */
            ->with('type_owner', 'editing_group', 'inheriting_group', 'new_elements_group','type_attributes')
            ;

        if ($id) {
            $build->where('element_types.id', $id);
        }
        if ($user_id) {
            $build->where('element_types.user_id', $user_id);
        }

        return $build;
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
        $build = null;
        $ret = null;
        $first_id = null;
        try {
            if ($field) {
                $build = $this->where($field, $value);
            } else {
                if (Utilities::is_uuid($value)) {
                    //the ref
                    $build = $this->where('ref_uuid', $value);
                } else {
                    if (is_string($value)) {
                        $parts = explode('.', $value);
                        if (count($parts) >= 2) {
                            $owner_hint = $parts[0];
                            $maybe_name = $parts[1];
                            /**
                             * @var User $owner
                             */
                            $owner = (new User)->resolveRouteBinding($owner_hint);
                            $build = $this->where('user_id', $owner?->id)->where('type_name', $maybe_name);
                        }
                    }
                }
            }
            if ($build) {
                $first_id = (int)$build->value('id');
                if ($first_id) {
                    $ret = ElementType::buildElementType(id:$first_id)->first();
                }
            }
        }
        catch (\Exception $e) {
            Log::warning('Element Type resolving: '. $e->getMessage());
        }
        finally {
            if (empty($ret) || empty($first_id) || empty($build)) {
                throw new HexbatchNotFound(
                    __('msg.element_type_not_found',['ref'=>$value]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                    RefCodes::ELEMENT_TYPE_NOT_FOUND
                );
            }
        }
        return $ret;

    }

    public function getName() :string {
        return $this->type_owner->username.'.'.$this->type_name;
    }

    public function isInUse() : bool {
        return false;
    }

    public function canUserEdit(User $user) : bool {
        if ($this->type_owner?->inAdminGroup($user->id) ) { return true; }
        if ($this->editing_group?->isMember($user->id) ) { return true; }
        return false;
    }

    public function canUserViewDetails(User $user) : bool {
        if ($this->type_owner?->inAdminGroup($user->id) ) { return true; }
        if ($this->editing_group?->isMember($user->id) ) { return true; }
        if ($this->inheriting_group?->isMember($user->id) ) { return true; }
        return false;
    }
}
