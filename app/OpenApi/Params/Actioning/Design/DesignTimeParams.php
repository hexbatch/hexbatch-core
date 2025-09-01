<?php

namespace App\OpenApi\Params\Actioning\Design;

use App\Models\TimeBound;
use App\OpenApi\ApiDataBase;
use App\OpenApi\Common\HexbatchCron;
use App\OpenApi\Common\Resources\HexbatchResourceName;
use Illuminate\Support\Collection;
use OpenApi\Attributes as OA;


#[OA\Schema(schema: 'DesignTimeParams')]
class DesignTimeParams extends ApiDataBase
{
    #[OA\Property(ref: '#/components/schemas/HexbatchResourceName', title: 'Name', description: 'Name of the bound')]
    protected ?string $bound_name = null;


    #[OA\Property( title: 'Starting at',description: "Optional Iso 8601 datetime", format: 'datetime',example: "2025-01-25T15:00:59-06:00",nullable: true)]
    protected ?string $bound_start = null;

    #[OA\Property( title: 'Stopping at',description: "Optional Iso 8601 datetime", format: 'datetime',example: "2025-02-25T15:00:59-06:00",nullable: true)]
    protected ?string $bound_stop = null;

    #[OA\Property(title: 'Cron', description: 'Optional linux cronjob tab string', type: HexbatchCron::class)]
    protected ?string $bound_cron = null;


    #[OA\Property(title: 'Cron Timezone', description: 'The timezone the cron is running in')]
    protected ?string $bound_cron_timezone = null;

    #[OA\Property(title: 'Cron period length', description: 'The amount of seconds after each cron match',minimum: 1)]
    protected ?int $bound_period_length = null;

    public function __construct(
        protected ?TimeBound     $given_bound = null
    )
    {

    }

    public function fromCollection(Collection $col, bool $do_validation = true)
    {
        parent::fromCollection($col);

        if ($col->has('bound_name') && $col->get('bound_name')) {
            $this->bound_name = (string)$col->get('bound_name');
        }

        if ($col->has('bound_start') && $col->get('bound_start')) {
            $this->bound_start = (string)$col->get('bound_start');
        }

        if ($col->has('bound_stop') && $col->get('bound_stop')) {
            $this->bound_stop = (string)$col->get('bound_stop');
        }

        if ($col->has('bound_cron') && $col->get('bound_cron')) {
            $this->bound_cron = (string)$col->get('bound_cron');
        }

        if ($col->has('bound_cron_timezone') && $col->get('bound_cron_timezone')) {
            $this->bound_cron_timezone = (string)$col->get('bound_cron_timezone');
        }

        if ($col->has('bound_period_length') && $col->get('bound_period_length')) {
            $this->bound_period_length = (int)$col->get('bound_period_length');
        }
    }

    public function getBoundName(): ?string
    {
        return $this->bound_name;
    }

    public function getBoundStart(): ?string
    {
        return $this->bound_start;
    }

    public function getBoundStop(): ?string
    {
        return $this->bound_stop;
    }

    public function getBoundCron(): ?string
    {
        return $this->bound_cron;
    }

    public function getBoundCronTimezone(): ?string
    {
        return $this->bound_cron_timezone;
    }

    public function getBoundPeriodLength(): ?int
    {
        return $this->bound_period_length;
    }

    public function getBoundUuid(): ?string
    {
        return $this->given_bound?->ref_uuid;
    }




}
