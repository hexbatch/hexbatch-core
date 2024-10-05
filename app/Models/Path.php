<?php

namespace App\Models;


use App\Enums\Paths\PathRelationshipType;
use App\Enums\Paths\PathReturnsType;
use App\Enums\Paths\TimeComparisonType;
use App\Enums\Rules\TypeOfChildLogic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int path_owning_namespace_id
 * @property int parent_path_id
 * @property int path_type_id
 * @property int path_server_id
 * @property int path_attribute_id
 * @property int sorting_attribute_id
 * @property int path_element_set_id
 * @property int path_namespace_id
 * @property int path_location_bound_id
 * @property int path_min_gap
 * @property int path_max_gap
 * @property int path_min_count
 * @property int path_max_count
 * @property int path_result_limit
 * @property bool is_partial_matching_name
 * @property bool is_sorting_order_asc
 * @property string ref_uuid
 * @property string path_start_at
 * @property string path_end_at
 * @property string path_part_name
 * @property string value_json_path
 * @property string sort_json_path
 * @property string path_compiled_sql
 *
 * @property TypeOfChildLogic path_logic
 * @property PathRelationshipType path_relationship
 * @property TimeComparisonType time_comparison
 * @property PathReturnsType path_returns
 *
 * @property string created_at
 * @property string updated_at
 */
class Path extends Model
{

    protected $table = 'paths';
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
        'path_relationship' => PathRelationshipType::class,
        'time_comparison' => TimeComparisonType::class,
        'path_returns' => PathReturnsType::class,
        'path_logic' => TypeOfChildLogic::class,
    ];

    /**
     * @throws \Exception
     */
    public static function collectPath(Collection|string $collect) : Path {
        try {
            DB::beginTransaction();
            $path = new Path();

            DB::commit();
            return $path;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

}
