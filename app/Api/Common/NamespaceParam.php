<?php

namespace App\Api\Common;

use App\Api\Calls\User\HexbatchUserName;
use OpenApi\Attributes as OA;

#[OA\PathParameter(
    name: 'namespace', description: "Namespace",
    in: 'path', required: true,  schema: new OA\Schema(ref: HexbatchUserName::class )
)]
class NamespaceParam
{


}
