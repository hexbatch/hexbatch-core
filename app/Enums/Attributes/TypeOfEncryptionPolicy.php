<?php
namespace App\Enums\Attributes;
/**
 * postgres enum type_merge_json
 */
enum TypeOfEncryptionPolicy : string {
    case NO_ENCRYPTION = 'no_encryption';
    case NAMESPACE_ENCRYPTS = 'namespace_encrypts';
    case SERVER_ENCRYPTS = 'server_encrypts';

    public static function tryFromInput(string|int|bool|null $test ) : TypeOfEncryptionPolicy {
        $maybe  = TypeOfEncryptionPolicy::tryFrom($test);
        if (!$maybe ) {
            $delimited_values = implode('|',array_column(TypeOfEncryptionPolicy::cases(),'value'));
            throw new \InvalidArgumentException(__("msg.invalid_enum",['ref'=>$test,'enum_list'=>$delimited_values]));
        }
        return $maybe;
    }
}


