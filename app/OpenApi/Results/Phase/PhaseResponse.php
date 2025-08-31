<?php

namespace App\OpenApi\Results\Phase;

use App\Models\Phase;
use App\OpenApi\Common\HexbatchUuid;
use App\OpenApi\Results\ResultBase;
use App\OpenApi\Results\Types\TypeResponse;
use Carbon\Carbon;
use OpenApi\Attributes as OA;


/**
 * Show details about a phase
 */
#[OA\Schema(schema: 'PhaseResponse')]
class PhaseResponse extends ResultBase
{
    #[OA\Property(title: 'Phase uuid',type: HexbatchUuid::class)]
    public string $uuid = '';

    #[OA\Property(title: 'Type of phase')]
    public ?TypeResponse $phase_type = null ;

    #[OA\Property(title: 'Is default')]
    public bool $is_default  ;

    #[OA\Property(title: 'Is system')]
    public bool $is_system  ;


    #[OA\Property(title: 'Edited by phase uuid',type: HexbatchUuid::class)]
    public string $edited_by_phase_uuid = '';
    #[OA\Property(title: 'Type of phase')]
    public ?PhaseResponse $edited_by_phase = null ;


    #[OA\Property(title: 'Namespace created at',format: 'date-time')]
    public ?string $created_at = '';






    public function __construct(Phase $given_phase,int $type_level = 0,int $attribute_level = 0,int $phase_level = 0)
    {
        $this->uuid = $given_phase->ref_uuid;
        if ($type_level > 0 ) {
            /** @uses Phase::phase_type()  */
            $this->phase_type = new TypeResponse(given_type: $given_phase->phase_type);
        }

        if ($phase_level > 0 && $given_phase->edited_by_phase) {
            /** @uses Phase::edited_by_phase()  */
            $this->edited_by_phase = new PhaseResponse(given_phase: $given_phase->edited_by_phase,
                type_level: $type_level,attribute_level: $attribute_level,phase_level: $phase_level - 1);
        }
        $this->edited_by_phase_uuid = $given_phase->edited_by_phase?->ref_uuid;

        $this->is_default = $given_phase->is_default_phase;
        $this->is_system = $given_phase->is_system;
        $this->created_at = $given_phase->created_at? Carbon::parse($given_phase->created_at,'UTC')
            ->timezone(config('app.timezone'))->toIso8601String():null;

    }


    public  function toArray() : array  {
        $ret = parent::toArray();
        $ret['uuid'] = $this->uuid;
        $ret['is_default'] = $this->is_default;
        $ret['is_system'] = $this->is_system;
        $ret['created_at'] = $this->created_at;
        $ret['edited_by_phase_uuid'] = $this->edited_by_phase_uuid;
        if ($this->edited_by_phase) {
            $ret['edited_by_phase'] = $this->edited_by_phase;
        }

        if ($this->phase_type) {
            $ret['phase_type'] = $this->phase_type;
        }
        return $ret;
    }

}
