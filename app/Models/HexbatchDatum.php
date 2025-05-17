<?php

namespace App\Models;


use App\Enums\Things\TypeOfHexbatchDataSource;
use App\Enums\Things\TypeOfHexbatchDataStatus;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int collection_attribute_id
 * @property int collection_type_id
 * @property int collection_element_id
 * @property int collection_set_member_id
 * @property int collection_set_id
 * @property int collection_namespace_id
 * @property int collection_path_id
 * @property int collection_user_id
 * @property bool is_cursor
 * @property ArrayObject collection_json
 * @property TypeOfHexbatchDataSource thing_data_source
 *
 */
class HexbatchDatum extends Model
{

    protected $table = 'hexbatch_data';
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
    protected $casts = [
        'collection_json' => AsArrayObject::class,
        'thing_data_source' => TypeOfHexbatchDataSource::class,
    ];

    const string LOGIC_STATE_KEY = 'hex_logic_state';

    public function isJsonFalse() : bool {
        //if it has non-null json but that json is empty array or empty object or false primitive as first element of array,
        // or false value in top object key of logic_state (if provided) its false
        if ($this->collection_json === null) {return false;}
        if (empty($this->collection_json->count())) {
            return false;
        }
        if ($this->collection_json->offsetExists(static::LOGIC_STATE_KEY)) {
            $what = $this->collection_json->offsetGet(static::LOGIC_STATE_KEY);
            if (!empty($what)) {
                return false;
            }
        }

        return true;
    }

    public static function buildThingData(
        ?int $id = null,
        ?int $collection_type_id = null,
        ?int $collection_namespace_id = null,
        ?TypeOfHexbatchDataStatus $thing_status = null
    )
    : Builder
    {

        /**
         * @var Builder $build
         */
        $build =  HexbatchDatum::select('hexbatch_data.*')

        ;

        if ($id) {
            $build->where('hexbatch_data.id',$id);
        }

        if ($collection_type_id) {
            $build->where('hexbatch_data.collection_type_id',$collection_type_id);
        }

        if ($collection_namespace_id) {
            $build->where('hexbatch_data.collection_namespace_id',$collection_namespace_id);
        }

        if ($thing_status) {
           throw new \RuntimeException("not implemented");
        }




        return $build;
    }

}
