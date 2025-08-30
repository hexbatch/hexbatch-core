<?php

namespace App\OpenApi\Common;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'hexbatch_cron',
    title: 'Linux crontab schedule',
    description: 'Defines the cron format',
    type: 'string',
    maxLength: 36,
    minLength: 36,
    pattern: '^((((\d+,)+\d+|(\d+(\/|-|#)\d+)|\d+(L|W)?|\*(\/\d+)?|L(-\d+|W)?|\?|[A-Z]{3}(-[A-Z]{3})?) ?){5,7})|(@(annually|yearly|monthly|weekly|daily|hourly))$',
    example: [new OA\Examples(summary: "Example cron", value:'15 5 * * THU') ]
)]
class HexbatchCron
{


}
