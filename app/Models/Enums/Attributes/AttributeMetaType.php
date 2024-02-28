<?php
namespace App\Models\Enums\Attributes;
enum AttributeMetaType : string {
    case NONE = 'none';
    case NAME = 'name';
    case AUTHOR = 'author';
    case COPYWRITE = 'copywrite';
    case URL = 'url';
    case RATING = 'rating';
    case MIME_TYPE = 'mime_type';
}

