<?php

namespace App\Helpers\Attributes;

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

    public function __construct(Request $request)
    {
        $options_block = new Collection();
        if ($request->request->has('options')) {
            $options_block = $request->collect('options');
        }



        $this->is_constant = false;
        $this->is_static = false;
        $this->is_human = false;
        $this->is_final = false;

        foreach ($options_block as $key => $val) {
            if (property_exists($this,$key)) {
                $this->$key = Utilities::boolishToBool($val);
            }
        }
    }

    public function assign(Attribute $attribute) {
        foreach ($this as $key => $val) {
            $attribute->$key = $val;
        }
    }


}
