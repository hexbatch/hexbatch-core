<?php

namespace App\Sys\Res\Atr\Stk\Placeholder;



use App\Enums\Attributes\TypeOfElementValuePolicy;
use App\Enums\Attributes\TypeOfServerAccess;
use App\Sys\Res\Atr\BaseAttribute;

class MarkerData extends BaseAttribute
{
    const UUID = 'afa6f45f-7bef-4a8f-bc17-05edcd8fd5ad';
    const ATTRIBUTE_NAME = 'marker_data';

    const IS_FINAL = true;

    const DEFAULT_VALUE = null;
    const ?string JSON_READ_PATH = null;
    const ?string JSON_WRITE_PATH = null;

    const TypeOfElementValuePolicy VALUE_POLICY = TypeOfElementValuePolicy::PER_ELEMENT;
    const TypeOfServerAccess ACCESS_POLICY = TypeOfServerAccess::IS_ELEMENT_PRIVATE;

}


