<?php

namespace App\Models;


use App\Enums\Types\TypeOfApproval;

use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property string ref_uuid
 * @property int child_type_id
 * @property int parent_type_id
 * @property int parent_rank
 * @property TypeOfApproval parent_type_approval
 *
 * @property string created_at
 * @property string updated_at
 *
 *
 */
class ElementTypeParent extends Model
{

    protected $table = 'element_type_parents';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'child_type_id',
        'parent_type_id',
        'parent_rank',
        'approval',
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
    protected $casts = [
        'parent_type_approval' => TypeOfApproval::class
    ];


    public function getName() :string {
        return $this->ref_uuid;
    }


    /**
     * @throws \Exception
     */
    public static function addParent(ElementType $parent, ElementType $child) :ElementTypeParent {


        //parent is not checked for validity until the publish event
        try {
            DB::beginTransaction();
            if ( $parent->is_final_type) {
                throw new HexbatchNotPossibleException(__('msg.parent_type_is_not_inheritable'),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::TYPE_CANNOT_INHERIT);
            }

            $par = new ElementTypeParent();

            $current_step = ElementTypeParent::where('child_type_id', $child->id)
                ->where('parent_type_id', $parent->id)
                ->max('parent_rank') ?? 0;

            if (!$current_step) {
                $current_step = ElementTypeParent::where('child_type_id', $child->id)->max('parent_rank') ?? 0;
            }
            //will check when the type is published
            $par->upsert([
                'child_type_id' => $child->id,
                'parent_type_id' => $parent->id,
                'parent_rank' => $current_step + 1,
            ], ['parent_type_id', 'child_type_id']);




            DB::commit();
            return $par;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }



}
