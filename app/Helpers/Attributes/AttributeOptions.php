<?php

namespace App\Helpers\Attributes;

use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AttributeOptions
{


    public $is_constant;
    public $is_static;
    public $is_final;
    public $is_human;
    public $is_read_policy_all;
    public $is_write_policy_all;

    public function __construct(Request $request)
    {
        $options = new Collection();
        if ($request->request->has('options')) {
            $options_block = $request->collect('options');
            $options->merge($options_block);
        }

        if ($request->request->has('permissions')) {
            $permissions_block = $request->collect('permissions');
            if ($permissions_block->has('set_requirements')) {
                $requirements_block = collect($permissions_block->get('permissions'));
                $options->merge($requirements_block);
            }
        }

        $this->is_constant = false;
        $this->is_static = false;
        $this->is_human = false;
        $this->is_final = false;
        $this->is_read_policy_all = false;
        $this->is_write_policy_all = false;

        foreach ($options as $key => $val) {
            if (property_exists($this,$key)) {
                $this->$key = (bool)$val;
            }
        }

    }

    public function assign(Attribute $attribute) {
        foreach ($this as $key => $val) {
            $attribute->$key = $val;
        }
    }


}
