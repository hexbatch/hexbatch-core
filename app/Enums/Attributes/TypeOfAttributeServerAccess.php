<?php
namespace App\Enums\Attributes;
enum TypeOfAttributeServerAccess : string {

    case PUBLIC = 'public';
    case PRIVATE_TO_HOME_SERVER = 'private_to_home_server';
    case WHITELISTED_SERVERS = 'whitelisted_servers';
    case WHITELISTED_SERVERS_READ_ONLY = 'whitelisted_servers_read_only';
    case OTHER_SERVERS_READ_ONLY = 'other_servers_read_only';

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfAttributeServerAccess {
        $maybe  = TypeOfAttributeServerAccess::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfAttributeServerAccess::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


