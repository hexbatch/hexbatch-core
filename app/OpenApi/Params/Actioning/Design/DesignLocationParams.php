<?php

namespace App\OpenApi\Params\Actioning\Design;


use App\Enums\Bounds\TypeOfLocation;
use App\Helpers\Utilities;
use App\Models\LocationBound;
use App\OpenApi\ApiCallBase;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;


#[OA\Schema(schema: 'DesignLocationParams')]
class DesignLocationParams extends ApiCallBase
{
    #[OA\Property(ref: '#/components/schemas/HexbatchResourceName', title: 'Name', description: 'Name of the bound', nullable: true)]
    protected ?string $bound_name = null;


    #[OA\Property( title: 'Location type',description: "Locations are maps or shapes")]
    protected TypeOfLocation $location_type ;

    #[OA\Property( title: 'Geo Json',description: "Geo json supported", nullable: true)]
    /** @var mixed[] $geo_json */
    protected array $geo_json = [];

    #[OA\Property( title: 'Display info',description: "Add color and texture here", nullable: true)]
    /** @var mixed[] $display */
    protected array $display = [];


    public function __construct(
        protected ?LocationBound     $given_bound = null
    )
    {
        parent::__construct();
    }

    public function fromCollection(Collection $col, bool $do_validation = true)
    {
        parent::fromCollection($col);

        if ($col->has('bound_name') && $col->get('bound_name')) {
            $this->bound_name = (string)$col->get('bound_name');
        }

        if ($col->has('location_type') && $col->get('location_type')) {
            $this->location_type = TypeOfLocation::tryFromInput($col->get('location_type'));
        }

        if ($col->has('geo_json') && $col->get('geo_json')) {
            $raw_default = $col->get('geo_json');
            if (!is_array($raw_default)) {
                if (is_object($raw_default)) {
                    $raw_default = Utilities::toArrayOrNull($raw_default);
                } else {
                    $raw_default = [$raw_default];
                }
            }
            $this->geo_json = $raw_default;
        }

        if ($col->has('display') && $col->get('display')) {
            $raw_default = $col->get('display');
            if (!is_array($raw_default)) {
                if (is_object($raw_default)) {
                    $raw_default = Utilities::toArrayOrNull($raw_default);
                } else {
                    $raw_default = [$raw_default];
                }
            }
            $this->display = $raw_default;
        }
    }

    public function getBoundName(): ?string
    {
        return $this->bound_name;
    }


    public function getBoundUuid(): ?string
    {
        return $this->given_bound?->ref_uuid;
    }

    public function getLocationType(): ?TypeOfLocation
    {
        return $this->location_type;
    }

    public function getGeoJson(): array
    {
        return $this->geo_json;
    }

    public function getDisplay(): array
    {
        return $this->display;
    }




}
