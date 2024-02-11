<?php

namespace App\Helpers\Attributes\Build;

use App\Helpers\Utilities;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AttributeBinaryOptions
{


    public bool $is_constant;
    public bool $is_static;
    public bool $is_final;
    public bool $is_human;

    public bool $b_skip = false;

    public function __construct(Request $request)
    {
        $options_block = new Collection();
        if ($request->request->has('options')) {
            $options_block = $request->collect('options');
        }
        if (!$options_block->count()) {
            $this->b_skip = true;
            return;
        }


        $this->is_constant = false;
        $this->is_static = false;
        $this->is_human = false;
        $this->is_final = false;

        foreach ($options_block as $key => $val) {
            if (property_exists($this,$key)) {
                if ($key === 'b_skip') {continue;}
                $this->$key = Utilities::boolishToBool($val);
            }
        }
    }

    public function assign(Attribute $attribute) {
        if ($this->b_skip) {return;}

        foreach ($this as $key => $val) {
            if ($key === 'b_skip') {continue;}
            $attribute->$key = $val;
        }
    }


}
