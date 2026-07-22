<?php


namespace App\Sys\Res\Types;

use App\Enums\Attributes\TypeOfServerAccess;
use App\Sys\Res\Atr\INewSystemAttribute;

interface INewSystemType
{
    public static function getTypeUuid(): string;
    public static function getTypeName(): string;
    public static function isTypeFinal() : bool;

    public static function getTypeAccessPolicy() : TypeOfServerAccess;

    /**
     * @return array<string>
     */
    public static function getParentUuids() : array ;

    /**
     * @return array<INewSystemAttribute>
     */
    public static function getAttributeClasses() : array ;
}
