<?php

namespace App\Api\Common;


use App\Api\IApiOaResponse;
use App\Models\ElementType;
use App\Models\Thing;
use Carbon\Carbon;
use OpenApi\Attributes as OA;

/**
 * Show details about the logged-in user
 */
#[OA\Schema(schema: 'ThingResponse')]
class ThingResponse implements IApiOaResponse
{
    #[OA\Property(title: 'Thing unique id',type: HexbatchUuid::class)]
    public string $uuid;

    #[OA\Property(title: 'Thing parent unique id',type: HexbatchUuid::class)]
    public ?string $parent_uuid;

    #[OA\Property(title: 'Status')]
    public string $status;

    #[OA\Property(title: 'Api Name')]
    public string $api_name;

    #[OA\Property(title: 'Is Waiting')]
    public bool $waiting;


    #[OA\Property(title: 'When the thing was made',format: 'date-time')]
    public string $created_at;

    #[OA\Property(title: 'Children')]
    /** @var ThingResponse[] $children */
    public array $children;


    public function __construct(Thing $thing)
    {
        $this->uuid = $thing->ref_uuid;
        $this->parent_uuid = $thing->thing_parent?->ref_uuid;
        $this->status = $thing->thing_status->value;
        if ($thing->api_or_action_type_id) {
            $type = ElementType::getElementType(id: $thing->api_or_action_type_id);
            $this->api_name = $type->getName();
        }
        $this->waiting = false;
        if ($thing->is_waiting_on_hook) { $this->waiting = true;}

        $this->created_at = Carbon::createFromTimestamp($thing->created_at_ts,config('app.timezone'))->toIso8601String();

        $this->children = [];
        if (count($thing->thing_children)) {
            foreach ($thing->thing_children as $child) {
                $this->children[] = new ThingResponse($child);
            }
        }

    }


}
