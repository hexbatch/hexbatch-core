<?php

namespace App\OpenApi\Bounds;

use App\Api\Common\HexbatchUuid;
use App\Enums\Bounds\TypeOfLocation;
use App\Helpers\Utilities;
use App\Models\LocationBound;
use Carbon\Carbon;
use JsonSerializable;
use OpenApi\Attributes as OA;


/**
 * Show details about a map or shape location
 */
#[OA\Schema(schema: 'LocationResponse')]
class LocationResponse implements  JsonSerializable
{
    #[OA\Property(title: 'Location uuid',type: HexbatchUuid::class)]
    public string $uuid ;

    #[OA\Property(title: 'Name')]
    public string $name = '';

    #[OA\Property(title: 'Location Type')]
    public TypeOfLocation $location_type ;

    #[OA\Property( title: "Geo Json", items: new OA\Items(), nullable: true)]
    protected ?array $geo_json = null;

    #[OA\Property( title: "Display", items: new OA\Items(), nullable: true)]
    protected ?array $location_display = null;

    #[OA\Property( title: "Bounding box", items: new OA\Items(), nullable: true)]
    protected ?array $location_bounding_box = null;

    #[OA\Property(title: 'Location created at',format: 'date-time')]
    public ?string $created_at = '';





    public function __construct(LocationBound $given_location)
    {
        $this->uuid = $given_location->ref_uuid;
        $this->name = $given_location->getName();
        $this->location_type = $given_location->location_type;
        $this->geo_json = Utilities::maybeDecodeJson($given_location->geom_as_geo_json);
        $this->location_display = $given_location->display_json?->getArrayCopy();
        $this->location_bounding_box = $given_location->getBoundingBox();
        $this->created_at = $given_location->created_at?
            Carbon::parse($given_location->created_at,'UTC')->timezone(config('app.timezone'))->toIso8601String():null;
    }


    public function jsonSerialize(): array
    {
        $ret = [];
        $ret['uuid'] = $this->uuid;
        $ret['name'] = $this->name;
        $ret['location_type'] = $this->location_type->value;
        $ret['geo_json'] = $this->geo_json;
        $ret['location_bounding_box'] = $this->location_bounding_box;
        $ret['location_display'] = $this->location_display;
        $ret['created_at'] = $this->created_at;
        return $ret;
    }

}
