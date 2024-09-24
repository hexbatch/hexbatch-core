<?php

namespace App\Helpers\Bounds;


use App\Exceptions\HexbatchCoreException;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\TimeBound;
use App\Rules\BoundNameReq;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TimeBoundGathering
{
    public ?TimeBound $current_bound;
    public ?string $bound_start =null;
    public ?string $bound_stop =null;
    public ?int $bound_period_length =null;
    public ?string $bound_cron =null;
    public ?string $bound_cron_timezone =null;
    public ?string $bound_name =null;
    public function __construct(Collection $request,?TimeBound $current_bound = null)
    {
        $this->current_bound = $current_bound;

        if ($request->has('bound_name')) {
            $test  = $request->get('bound_name');
            if (is_string($test) && Str::trim($test)) {
                $this->bound_name = Str::trim($test);
                $this->validateName($this->bound_name);
            }

        }

        if ($request->has('bound_start')) {
            $test = $request->get('bound_start');
            if (is_string($test) && Str::trim($test)) {
                $this->bound_start = Str::trim($test);
            }
        }

        if ($request->has('bound_stop')) {
            $test = $request->get('bound_stop');
            if (is_string($test) && Str::trim($test)) {
                $this->bound_stop = Str::trim($test);
            }
        }

        if ($request->has('bound_cron')) {
            $test = $request->get('bound_cron');
            if (is_string($test) && Str::trim($test)) {
                $this->bound_cron = Str::trim($test);
            }
        }

        if ($request->has('bound_cron_timezone')) {
            $test = $request->get('bound_cron_timezone');
            if (is_string($test) && Str::trim($test)) {
                $this->bound_cron_timezone = Str::trim($test);
            }
        }

        if ($request->has('bound_period_length')) {
            $test = $request->get('bound_period_length');
            if (intval($test) && intval($test) > 0) {
                $this->bound_period_length = intval($test);
            }
        }
    }

    public function assign() : TimeBound {
        if (!$this->current_bound) {
            $this->current_bound = new TimeBound();
        }
        $node = $this->current_bound;

        if ($this->bound_name !== null ) { $node->bound_name = $this->bound_name; }
        if ($this->bound_start !== null ) { $node->bound_start = $this->bound_start; }
        if ($this->bound_stop !== null ) { $node->bound_stop = $this->bound_stop; }
        if ($this->bound_cron !== null ) { $node->bound_cron = $this->bound_cron; }
        if ($this->bound_cron_timezone !== null ) { $node->bound_cron_timezone = $this->bound_cron_timezone; }
        if ($this->bound_period_length !== null ) { $node->bound_period_length = $this->bound_period_length; }

        if (!$node->bound_name || !$node->bound_stop || !$node->bound_start) {
            throw new HexbatchCoreException(__("msg.time_bounds_needs_minimum_info"),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::BOUND_NEEDS_MIN_INFO);
        }

        //this saves too
        $node->setTimes(start: $node->bound_start,stop:$node->bound_stop,
            bound_cron: $node->bound_cron,period_length: $node->bound_period_length,
            bound_cron_timezone: $node->bound_cron_timezone); //saves and processes

        $out = TimeBound::buildTimeBound(id: $node->id)->first();
        return $out;
    }

    protected function validateName(?string $name) {
        if (!$name) {return;}
        try {
            Validator::make(['time_bound_name' => $name], [
                'time_bound_name' => ['required', 'string', new BoundNameReq()],
            ])->validate();
        } catch (ValidationException $v) {
            throw new HexbatchNotPossibleException($v->getMessage(),
                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                RefCodes::BOUND_INVALID_NAME);
        }
    }
}
