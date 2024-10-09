<?php
namespace App\Enums\Attributes;
/**
 * postgres enum type_of_server_access
 */
enum TypeOfAttributeServerAccess : string {

    case PUBLIC_TO_ALL = 'public_to_all_servers';
    case ONLY_HOME_SERVER = 'only_home_server';
    case WHITELISTED_SERVERS = 'public_to_whitelisted_servers';
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


