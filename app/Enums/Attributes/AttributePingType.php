<?php
namespace App\Enums\Attributes;
enum AttributePingType : string {
    case READ = 'read_all';
    case WRITE = 'write_all';
    case ALL = 'all';

    case ALL_TIME = 'all_time';
    case READ_TIME = 'read_time';
    case WRITE_TIME = 'write_time';

    case ALL_MAP = 'all_map';
    case READ_MAP = 'read_map';
    case WRITE_MAP = 'write_map';

    case ALL_SHAPE = 'all_shape';
    case READ_SHAPE = 'read_shape';
    case WRITE_SHAPE = 'write_shape';

    case ALL_USER = 'all_user';
    case READ_USER = 'read_user';
    case WRITE_USER = 'write_user';

    public static function tryFromInput(string|int|bool|null $test ) : AttributePingType {
        $maybe  = AttributePingType::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(AttributePingType::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }

}



