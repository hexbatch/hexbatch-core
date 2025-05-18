<?php

namespace App\OpenApi\App;

use App\Models\User;

use Carbon\Carbon;
use Hexbatch\Things\Helpers\ThingUtilities;
use OpenApi\Attributes as OA;

/**
 * This can describe a user or employee or someone who is both
 */
#[OA\Schema(schema: 'AboutResponse',title: "User")]
class AboutResponse
{

    #[OA\Property( title: 'App version',example: '1.1')]
    public string $app_version;


    #[OA\Property( title: 'Thing version',example: '2.0.9')]
    public string $thing_version;

    public function __construct()
    {
        $this->thing_version = ThingUtilities::getVersionAsString(true);
        $this->app_version = ThingUtilities::getVersionAsString(false);

    }


}
