<?php
namespace App\Models\Enums;
enum RemoteMapType : string {

    case NONE = 'none';
    case INPUT_ATTRIBUTE = 'input_attribute';
    case OUTPUT_ATTRIBUTE = 'output_attribute';
}
