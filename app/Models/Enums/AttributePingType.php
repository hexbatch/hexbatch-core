<?php
namespace App\Models\Enums;
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

}



