<?php

namespace App\Helpers\Attributes\Build;

use App\Models\Attribute;
use App\Models\AttributeMetum;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Meta
 */
class AttributeMetaGathering
{

    /**
     * @var AttributeMetum[] $meta_mori
     */
    public array $meta_mori;

    public bool $b_skip = false;




    public function __construct(Request $request)
    {
        $this->meta_mori = [];

        $meta_block = new Collection();
        if ($request->request->has('meta')) {
            $meta_block = $request->collect('meta');
        }

        if (!$meta_block->count()) {
            $this->b_skip = true;
            return;
        }

        foreach ($meta_block as $some_meta) {

            $this->meta_mori[] = AttributeMetum::createMetum(new Collection($some_meta));
        }

    }

    public function assign(Attribute $attribute) {
        if ($this->b_skip) {return;}
        try {
            DB::beginTransaction();

            foreach ($this->meta_mori as $what) {
                if (!$what) {continue;}
                $what->meta_parent_attribute_id = $attribute->id;
                if ($what->delete_mode) {
                    $what->deleteModeActivate();
                } else {
                    //see if id already exists for unique
                    $maybe = AttributeMetum::where('meta_parent_attribute_id',$what->meta_parent_attribute_id)
                        ->where('meta_type',$what->meta_type->value)
                        ->first();
                    if ($maybe) {
                        $what->id = $maybe->id;
                        $what->exists = true;
                    }
                    $what->save();
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            /** @noinspection PhpUnhandledExceptionInspection */
            throw $e;
        }
    }


}
