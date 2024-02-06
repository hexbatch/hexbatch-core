<?php
namespace App\Models\Enums;
enum AttributeValueType : string {
    case NUMERIC = 'numeric';
    case NUMERIC_INTEGER = 'numeric_integer';
    case NUMERIC_NATURAL = 'numeric_natural';
    case STRING = 'string';
    case JSON = 'json';
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

    case SCHEDULE_BOUNDS = 'schedule_bounds';
    case MAP_BOUNDS = 'map_bounds';
    case SHAPE_BOUNDS = 'shape_bounds';

    case SET = 'set ';
    case SEARCH = 'search';
    case VIEW = 'view';
    case MUTUAL = 'mutual';
    case CONTAINER = 'container';
    case COORDINATE_MAP = 'coordinate_map';
    case COORDINATE_SHAPE = 'coordinate_shape';
    const NUMERIC_TYPES = [
        self::NUMERIC,self::NUMERIC_INTEGER,self::NUMERIC_NATURAL
    ];

    const STRING_TYPES = [
        self::STRING,self::STRING_MARKDOWN,self::STRING_HTML,self::STRING_XML,self::STRING_BINARY
    ];

    const POINTER_TYPES = [
      self::USER,self::USER_GROUP,self::ATTRIBUTE,self::ELEMENT,self::ELEMENT_TYPE,self::SCRIPT,self::REMOTE,self::ACTION,
      self::SCHEDULE_BOUNDS,self::MAP_BOUNDS,self::SHAPE_BOUNDS,self::SET,self::VIEW,self::MUTUAL,self::CONTAINER, self::SEARCH
    ];

    const COORDINATION_TYPES = [
      self::COORDINATE_MAP,self::COORDINATE_SHAPE
    ];
}



