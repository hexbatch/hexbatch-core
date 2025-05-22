<?php

namespace App\Models;


use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Rules\NamespaceNameReq;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


/**
 *
 * todo add trigger to make only one phase the default in all the rows, all others must be false
 *
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int phase_type_id
 * @property int edited_by_phase_id
 * @property bool is_default_phase
 * @property bool is_system
 * @property string ref_uuid
 * @property string phase_name
 *
 * @property string created_at
 * @property string updated_at
 *
 */
class Phase extends Model
{

    protected $table = 'phases';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

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

    public static function getDefaultPhase() : ?Phase {
        return Phase::where('is_default_phase',true)->first();
    }

    public static function buildPhase(
        ?int            $me_id = null,
        ?int            $type_id = null,
        ?string         $uuid = null
    )
    : Builder
    {

        /** @var Builder $build */
        $build = Phase::select('phases.*')
            ->selectRaw(" extract(epoch from  phases.created_at) as created_at_ts,
                                    extract(epoch from  phases.updated_at) as updated_at_ts
                                    ");



        if ($me_id) {
            $build->where('phases.id', $me_id);
        }

        if ($type_id) {
            $build->where('phases.phase_type_id', $type_id);
        }

        if ($uuid) {
            $build->where('phases.ref_uuid', $uuid);
        }

        return $build;
    }

    public static function getThisPhase(
        ?int             $id = null,
        ?int             $type_id = null,
        ?string          $uuid = null
    )
    : Phase
    {
        $ret = static::buildPhase(me_id:$id,type_id: $type_id,uuid: $uuid)->first();

        if (!$ret) {
            $arg_types = [];
            $arg_vals = [];
            if ($id) { $arg_types[] = 'id'; $arg_vals[] = $id;}
            if ($type_id) { $arg_types[] = 'type_id'; $arg_vals[] = $type_id;}
            if ($uuid) { $arg_types[] = 'uuid'; $arg_vals[] = $uuid;}
            $arg_val = implode('|',$arg_vals);
            $arg_type = implode('|',$arg_types);
            throw new \InvalidArgumentException("Could not find phase via $arg_type : $arg_val");
        }
        return $ret;
    }

    public function setPhaseName(?string $name, ?string $attribute_name = null) {
        if (empty($attribute_name)) { $attribute_name = 'phase_name';}

        try {
            Validator::make([$attribute_name => $name], [
                $attribute_name => ['required', 'string',  new NamespaceNameReq()],
            ])->validate();
        } catch (ValidationException $v) {
            throw new HexbatchNotPossibleException($v->getMessage(),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::TYPE_INVALID_NAME);
        }
        $this->phase_name = $name;
    }

}
