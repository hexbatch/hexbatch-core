<?php
namespace App\Models\Enums\Attributes;
enum AttributeMetaType : string {
    case NONE = 'none';
    case AUTHOR = 'author';
    case COPYWRITE = 'copywrite';
    case URL = 'url';
    case RATING = 'rating';
    case LANG = 'lang';
    case MIME_TYPE = 'mime_type';
}
