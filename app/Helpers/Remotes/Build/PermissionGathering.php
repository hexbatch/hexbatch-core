<?php

namespace App\Helpers\Remotes\Build;

use App\Helpers\Utilities;
use App\Models\Remote;
use Illuminate\Http\Request;

class PermissionGathering
{

    const DEFAULT_UNUSED_NUMBER = -1;


    public ?bool $is_readable = null;
    public ?bool $is_writable = null;






    public function __construct(Request $request)
    {

        $top_block = $request->collect();
        if ($top_block->has('is_readable')) {
            $this->is_readable = Utilities::boolishToBool($top_block->get('is_readable'));
        }

        if ($top_block->has('is_writable')) {
            $this->is_writable = Utilities::boolishToBool($top_block->get('is_writable'));
        }



    }

    public function assign(Remote $remote) {

        foreach ($this as $key => $val) {
            if (is_null($val) || $val === static::DEFAULT_UNUSED_NUMBER ) { continue;}
            $remote->$key = $val;
        }



    }
}
