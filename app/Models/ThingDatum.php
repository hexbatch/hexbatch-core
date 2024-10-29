<?php

namespace App\Models;


use App\Enums\Things\TypeOfThingDataSource;
use App\Enums\Things\TypeOfThingStatus;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int owning_thing_id
 * @property int collection_attribute_id
 * @property int collection_type_id
 * @property int collection_element_id
 * @property int collection_set_member_id
 * @property int collection_set_id
 * @property int collection_namespace_id
 * @property int collection_path_id
 * @property bool is_cursor
 * @property ArrayObject collection_json
 * @property TypeOfThingDataSource thing_data_source
 *
 */
class ThingDatum extends Model
{

    protected $table = 'thing_data';
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
        'thing_data_source' => TypeOfThingDataSource::class,
    ];


    public static function buildThingData(
        ?int $id = null,
        ?int $collection_type_id = null,
        ?int $collection_namespace_id = null,
        ?TypeOfThingStatus $thing_status = null
    )
    : Builder
    {

        /**
         * @var Builder $build
         */
        $build =  ThingDatum::select('thing_data.*')

        ;

        if ($id) {
            $build->where('things.id',$id);
        }

        if ($collection_type_id) {
            $build->where('things.collection_type_id',$collection_type_id);
        }

        if ($collection_namespace_id) {
            $build->where('things.collection_namespace_id',$collection_namespace_id);
        }

        if ($thing_status) {
            $build->join('things',
                /**
                 * @param JoinClause $join
                 */
                function (JoinClause $join) use($thing_status) {
                    $join
                        ->on('things.id','=','things.owning_thing_id')
                        ->where('things.thing_status',$thing_status);
                }
            );
        }




        return $build;
    }

}
