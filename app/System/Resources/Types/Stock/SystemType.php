<?php

namespace App\System\Resources\Types\Stock;



use App\System\Resources\Namespaces\Stock\SystemUserNamespace;
use App\System\Resources\Types\BaseSystemType;

/**
 * all descendants have the same uuid across all servers but have a different parent (this)
 */
class SystemType extends BaseSystemType
{
    const UUID = '79a56b04-c36e-430f-bad4-5f53fb29ad4e';
    const NAMESPACE_UUID = SystemUserNamespace::UUID;

    const ATTRIBUTE_UUIDS = [];

    const PARENT_UUIDS = [];

    const DESCRIPTION_ELEMENT_UUID = '';

    const SERVER_UUID = '';

    const TYPE_NAME = 'root';

    public function getTypeName(): string { return static::TYPE_NAME;}

    public function isFinal(): bool { return false; }


}

/*
  * attributes that do not inherit by subtype, must have these as parent attr
 *  expiration
 */
