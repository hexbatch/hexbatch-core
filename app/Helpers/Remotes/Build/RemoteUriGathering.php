<?php

namespace App\Helpers\Remotes\Build;

use App\Models\Enums\RemoteUriDataFormatType;
use App\Models\Enums\RemoteUriMethod;
use App\Models\Enums\RemoteUriType;
use App\Models\Remote;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RemoteUriGathering
{
    const DEFAULT_UNUSED_NUMBER = -1;
    const DEFAULT_UNUSED_STRING = "__FACEFACE__";


    public ?RemoteUriType $uri_type = null;
    public ?RemoteUriMethod $uri_method = null;

    public ?int $uri_port = self::DEFAULT_UNUSED_NUMBER;
    public ?string $uri_string = self::DEFAULT_UNUSED_STRING;
    public ?RemoteUriDataFormatType $uri_data_input_format = null;
    public ?RemoteUriDataFormatType $uri_data_output_format = null;




    public function __construct(Request $request)
    {


        $uri_block = new Collection();
        if ($request->request->has('uri')) {
            $uri_block = $request->collect('uri');
        }

        if ($uri_block->has('uri_type')) {
            $convert = RemoteUriType::tryFrom($uri_block->get('uri_type'));
            $this->uri_type = $convert ?: null;
        }

        if ($uri_block->has('uri_method')) {
            $convert = RemoteUriMethod::tryFrom($uri_block->get('uri_method'));
            $this->uri_method = $convert ?: null;
        }

        if ($uri_block->has('uri_port')) {
            $this->uri_port = intval($uri_block->get('uri_port'));
            if ($this->uri_port <= 0) {$this->uri_port = null;}
        }

        if ($uri_block->has('uri_string')) {
            $this->uri_string = trim(($uri_block->get('uri_string')));
            if (empty($this->uri_string)) {$this->uri_string = null;}
        }

        if ($uri_block->has('uri_data_input_format')) {
            $convert = RemoteUriDataFormatType::tryFrom($uri_block->get('uri_data_input_format'));
            $this->uri_data_input_format = $convert ?: null;
        }

        if ($uri_block->has('uri_data_output_format')) {
            $convert = RemoteUriDataFormatType::tryFrom($uri_block->get('uri_data_output_format'));
            $this->uri_data_output_format = $convert ?: null;
        }

    }

    public function assign(Remote $remote) {

        foreach ($this as $key => $val) {
            if (is_null($val) || $val === static::DEFAULT_UNUSED_NUMBER || $val === static::DEFAULT_UNUSED_STRING) { continue;}
            $remote->$key = $val;
        }

    }
}
