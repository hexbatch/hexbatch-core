<?php
namespace App\Models\Enums\Attributes;
enum AttributeValueType : string {
    case NUMERIC = 'numeric';

    case STRING = 'string';
    case JSON = 'json';

    case USER = 'user';
    case USER_GROUP = 'user_group';
    case ATTRIBUTE = 'attribute';
    case ELEMENT = 'element';
    case ELEMENT_TYPE = 'element_type';
    case REMOTE = 'remote';
    case ACTION = 'action';

    case SCHEDULE_BOUNDS = 'schedule_bounds';
    case MAP_BOUNDS = 'map_bounds';
    case SHAPE_BOUNDS = 'shape_bounds';

    case SET = 'set ';
    case PATH = 'path';

    case COORDINATE_MAP = 'coordinate_map';
    case COORDINATE_SHAPE = 'coordinate_shape';

    case VIEW = 'view';
    case MUTUAL = 'mutual';
    case CONTAINER = 'container';
    case INTERFACE = 'interface';
    case PIPELINE = 'pipeline';
    case PIPELINE_JOINT = 'pipeline_joint';

    const SCALER_TYPES = [
        self::NUMERIC,self::STRING
    ];

    const POINTER_TYPES = [
      self::USER,self::USER_GROUP,self::ATTRIBUTE,self::ELEMENT,self::ELEMENT_TYPE,self::REMOTE,self::ACTION,
      self::SCHEDULE_BOUNDS,self::MAP_BOUNDS,self::SHAPE_BOUNDS,self::SET,self::VIEW,self::MUTUAL,self::CONTAINER, self::PATH,
      self::INTERFACE,self::PIPELINE,self::PIPELINE_JOINT
    ];

    const COORDINATION_TYPES = [
      self::COORDINATE_MAP,self::COORDINATE_SHAPE
    ];
}



