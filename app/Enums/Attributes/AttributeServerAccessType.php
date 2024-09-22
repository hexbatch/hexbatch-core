<?php
namespace App\Enums\Attributes;
enum AttributeServerAccessType : string {

    case PUBLIC = 'public';
    case PRIVATE_TO_HOME_SERVER = 'private_to_home_server';
    case WHITELISTED_SERVERS = 'whitelisted_servers';
    case WHITELISTED_SERVERS_READ_ONLY = 'whitelisted_servers_read_only';
    case OTHER_SERVERS_READ_ONLY = 'other_servers_read_only';


}


