<?php

namespace App\Models;

use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int element_parent_type_id
 * @property int element_namespace_id
 * @property string ref_uuid
 *
 * @property string created_at
 * @property string updated_at
 *
 * @property UserNamespace element_namespace
 *
 */
class Element extends Model
{

    /*
     * elements always stay on the originating server, but they can be copied
     */
    protected $table = 'elements';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'element_parent_type_id',
        'element_owner_user_id'
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
    public function element_namespace(): BelongsTo
    {
        return $this->belongsTo('App\Models\User', 'element_owner_user_id');
    }

    public static function buildElement(
        ?int $id = null)
    : Builder
    {

        /**
         * @var Builder $build
         */
        $build = Element::select('elements.*')
            ->selectRaw(" extract(epoch from  elements.created_at) as created_at_ts,  extract(epoch from  elements.updated_at) as updated_at_ts")
        ;

        if ($id) {
            $build->where('elements.id', $id);
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
                }
            }
            if ($build) {
                $first_id = (int)$build->value('id');
                if ($first_id) {
                    $ret = Element::buildElement(id:$first_id)->first();
                }
            }
        } finally {
            if (empty($ret) || empty($first_id) || empty($build)) {
                throw new HexbatchNotFound(
                    __('msg.element_not_found',['ref'=>$value]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                    RefCodes::ELEMENT_NOT_FOUND
                );
            }
        }
        return $ret;

    }

    public function getName() :string {
        return $this->ref_uuid;
    }
}
