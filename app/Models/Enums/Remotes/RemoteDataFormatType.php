<?php
namespace App\Models\Enums\Remotes;
enum RemoteDataFormatType : string {

    case TEXT = 'text';
    case XML = 'xml';
    case JSON = 'json';
    case YAML = 'yaml';

}
