<?php
namespace App\Models\Enums;
enum AttributeValueType : string {
    case NUMERIC = 'numeric';
    case NUMERIC_INTEGER = 'numeric_integer';
    case NUMERIC_NATURAL = 'numeric_natural';
    case STRING = 'string';
    case STRING_JSON = 'string_json';
    case STRING_MARKDOWN = 'string_markdown';
    case STRING_HTML = 'string_html';
    case STRING_XML = 'string_xml';
    case STRING_BINARY = 'string_binary';
    case USER = 'user';
    case USER_GROUP = 'user_group';
    case ATTRIBUTE = 'attribute';
    case ELEMENT = 'element';
    case ELEMENT_TYPE = 'element_type';
    case SCRIPT = 'script';
    case REMOTE = 'remote';
    case ACTION = 'action';
    case SEARCH = 'search';
    case SCHEDULE_BOUNDS = 'schedule_bounds';
    case MAP_BOUNDS = 'map_bounds';
    case SHAPE_BOUNDS = 'shape_bounds';
    case VIEW = 'view';
    case MUTUAL = 'mutual';
    case CONTAINER = 'container';
    case COORDINATE_MAP = 'coordinate_map';
    case COORDINATE_CARTESIAN = 'coordinate_cartesian';
}



