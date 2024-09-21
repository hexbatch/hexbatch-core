<?php
namespace App\Models\Enums\Attributes;
enum AttributeType : string {
    case VALUE = 'value';
    case RULE = 'rule';
    case META_AUTHOR = 'meta_author';
    case META_COPYWRITE = 'meta_copywrite';
    case META_URL = 'meta_url';
    case META_RATING = 'meta_rating';
    case META_ICU_LANGUAGE = 'meta_icu_language';
    case META_MIME_TYPE = 'meta_mime_type';
    case META_ICU_LOCALE = 'meta_icu_locale';
    case META_ICU_LOCATION = 'meta_icu_location';
    case READ_TIME_BOUNDS = 'read_time_bounds';
    case WRITE_TIME_BOUNDS = 'write_time_bounds';
    case READ_MAP_LOCATION_BOUNDS = 'read_map_location_bounds';
    case WRITE_MAP_LOCATION_BOUNDS = 'write_map_location_bounds';
    case READ_SHAPE_LOCATION_BOUNDS = 'read_shape_location_bounds';
    case WRITE_SHAPE_LOCATION_BOUNDS = 'write_shape_location_bounds';
}

