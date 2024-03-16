<?php

namespace App\Models;

use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property string ref_uuid
 * @property int user_id
 * @property string created_at
 * @property string updated_at
 *
 */
class Action extends Model
{

    protected $table = 'actions';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
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


    public static function buildAction(
        ?int $id = null)
    : Builder
    {

        /**
         * @var Builder $build
         */
        $build = Action::select('actions.*')
            ->selectRaw(" extract(epoch from  attributes.created_at) as created_at_ts,  extract(epoch from  attributes.updated_at) as updated_at_ts")
        ;

        if ($id) {
            $build->where('actions.id', $id);
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
                    $ret = Action::buildAction(id:$first_id)->first();
                }
            }
        } finally {
            if (empty($ret) || empty($first_id) || empty($build)) {
                throw new HexbatchNotFound(
                    __('msg.action_not_found',['ref'=>$value]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                    RefCodes::ACTION_NOT_FOUND
                );
            }
        }
        return $ret;

    }

    public function getName() :string {
        return $this->ref_uuid;
    }
}
