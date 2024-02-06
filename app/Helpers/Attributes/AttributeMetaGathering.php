<?php

namespace App\Helpers\Attributes;

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

    public ?AttributeMetum $description;
    public ?AttributeMetum $name; //string val only
    public ?AttributeMetum $standard_family; //string val only
    public ?AttributeMetum $author;
    public ?AttributeMetum $copywrite;
    public ?AttributeMetum $url; //string val only
    public ?AttributeMetum $rating; //string val only
    public ?AttributeMetum $internal;





    public function __construct(Request $request, bool $b_admin = false)
    {
        $this->description = null;
        $this->name = null;
        $this->author = null;
        $this->url = null;
        $this->internal = null;
        $this->copywrite = null;
        $this->rating = null;
        $this->standard_family = null;

        $meta_block = new Collection();
        if ($request->request->has('meta')) {
            $meta_block = $request->collect('meta');
        }

        foreach (AttributeMetum::PUBLIC_META as $public_meta_enum) {
            $type_meta = $public_meta_enum->value;
            if ($meta_block->has($type_meta)) {
                $this->$type_meta = AttributeMetum::createMetum(new Collection($meta_block->get($type_meta,[])));
            }
        }

        if ($b_admin) {
            foreach (AttributeMetum::ADMIN_META as $admin_meta_enum) {
                $type_meta = $admin_meta_enum->value;
                if ($meta_block->has($type_meta)) {
                    $this->$type_meta = AttributeMetum::createMetum(new Collection($meta_block->get($type_meta,[])));
                }
            }
        }

        if ($meta_block->has('description')) {
            $this->description = AttributeMetum::createMetum(new Collection($meta_block->get('description',[])));
        }

    }

    public function assign(Attribute $attribute) {
        try {
            DB::beginTransaction();
            /** @var AttributeMetum $what */
            foreach ($this as $what) {
                if (!$what) {continue;}
                $what->meta_parent_attribute_id = $attribute->id;
                if ($what->delete_mode) {
                    $what->deleteModeActivate();
                } else {
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
