<?php


namespace App\Sys\Res\Atr;

use App\Enums\Attributes\TypeOfElementValuePolicy;
use App\Enums\Attributes\TypeOfServerAccess;

interface INewSystemAttribute
{
    public static function getAttributeUuid(): string;
    public static function getAttributeParentUuid(): ?string;
    public static function getAttributeLocationUuid(): ?string;
    public static function getAttributeDesignUuid(): ?string;
    public static function getAttributeName(): string;
    public static function isAttributeFinal() : bool;
    public static function isAttributeAbstract() : bool;
    public static function getValuePolicy() : TypeOfElementValuePolicy;
    public static function getAccessPolicy() : TypeOfServerAccess;
    public static function getReadJsonPath() : ?string;
    public static function getValidateJsonPath() : ?string;
    public static function getAttributeDefaultValue() : ?array;


}


