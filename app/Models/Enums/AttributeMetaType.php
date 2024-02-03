<?php
namespace App\Models\Enums;
enum AttributeMetaType : string {
    case NONE = 'none';
    case DESCRIPTION = 'description';
    case NAME = 'name';
    case STANDARD_FAMILY = 'standard-family';
    case AUTHOR = 'author';
    case COPYWRITE = 'copywrite';
    case URL = 'url';
    case RATING = 'rating';
    case INTERAL = 'interal';
}

