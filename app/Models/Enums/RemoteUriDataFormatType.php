<?php
namespace App\Models\Enums;
enum RemoteUriDataFormatType : string {
//'none','','',''
    case NONE = 'none';
    case PLAIN_TEXT = 'plain_text';
    case XML = 'xml';
    case JSON = 'json';

}
