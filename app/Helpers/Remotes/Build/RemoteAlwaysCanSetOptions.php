<?php

namespace App\Helpers\Remotes\Build;

use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\Remote;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RemoteAlwaysCanSetOptions
{
    const DEFAULT_UNUSED_NUMBER = -1;
    public ?bool $is_retired = null;
    public ?bool $is_on = null;
    public ?int $timeout_seconds = self::DEFAULT_UNUSED_NUMBER;
    public ?int $rate_limit_unit_in_seconds = self::DEFAULT_UNUSED_NUMBER;
    public ?int $rate_limit_max_per_unit = self::DEFAULT_UNUSED_NUMBER;


    public ?bool $is_caching = null;
    public ?int $cache_ttl_seconds = self::DEFAULT_UNUSED_NUMBER;
    public ?array $cache_keys = null;


    public function __construct(Request $request)
    {
        $top_block = $request->collect();
        if ($top_block->has('is_on')) {
            $this->is_on = Utilities::boolishToBool($top_block->get('is_on'));
        }

        if ($top_block->has('is_retired')) {
            $this->is_retired = Utilities::boolishToBool($top_block->get('is_retired'));
        }

        if ($top_block->has('timeout_seconds')) {
            $this->timeout_seconds = intval($top_block->get('timeout_seconds'));
        }

        $call_schedule_block = new Collection();
        if ($request->request->has('call_schedule')) {
            $call_schedule_block = $request->collect('call_schedule');
        }

        if ($call_schedule_block->has('rate_limit_max_per_unit')) {
            $this->rate_limit_max_per_unit = intval($call_schedule_block->get('rate_limit_max_per_unit'));
            if ($this->rate_limit_max_per_unit <= 0) {$this->rate_limit_max_per_unit = null;}
        }

        if ($call_schedule_block->has('rate_limit_unit_in_seconds')) {
            $this->rate_limit_unit_in_seconds = intval($call_schedule_block->get('rate_limit_unit_in_seconds'));
            if ($this->rate_limit_unit_in_seconds <= 0) {$this->rate_limit_unit_in_seconds = null;}
        }

        $cache_block = new Collection();
        if ($request->request->has('cache')) {
            $cache_block = $request->collect('cache');
        }

        if ($cache_block->has('is_caching')) {
            $this->is_caching = Utilities::boolishToBool($cache_block->get('is_caching'));
        }

        if ($cache_block->has('cache_ttl_seconds')) {
            $this->cache_ttl_seconds = intval($cache_block->get('cache_ttl_seconds'));
            if ($this->cache_ttl_seconds <= 0) {$this->cache_ttl_seconds = null;}
        }

        if ($cache_block->has('cache_keys')) {
            $test_cache_keys = $cache_block->get('cache_keys');
            if (is_string($test_cache_keys)) {
                $json_issue = Utilities::jsonHasErrors($test_cache_keys);
                if ($json_issue) {
                    throw new HexbatchNotPossibleException(__("msg.this_is_bad_json", ['issue' => $json_issue]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::REMOTE_SCHEMA_ISSUE);
                }
                $this->cache_keys = json_decode($test_cache_keys, true);
            } else {
                $this->cache_keys = $test_cache_keys;
            }
            if (empty($this->cache_keys)) {
                $this->cache_keys = null;
            }
        }
    }

    public function assign(Remote $remote) {

        if ($this->is_on !== null) {
            $remote->powerRemote($this->is_on);
        }
        if ($this->is_on !== null) {
            $remote->powerRemote($this->is_on);
        }
        $counter = 0;
        foreach ($this as $key => $val) {
            if (is_null($val) || $val === static::DEFAULT_UNUSED_NUMBER) { continue;}
            $remote->$key = $val;
            $counter++;
        }

        if ($counter) {$remote->save();}

        if (
            intval($remote->getRawOriginal('rate_limit_max_per_unit')) !== $this->rate_limit_max_per_unit
            || intval($remote->getRawOriginal('rate_limit_unit_in_seconds')) !== $this->rate_limit_unit_in_seconds

        ) {
            $remote->resetRateLimit();
        }

        $older_cach_keys =  ($remote->getRawOriginal('cache_keys')??[]);
        $newer_cache_keys = ($this->cache_keys??[]);
        $diff_keys = array_diff($older_cach_keys, $newer_cache_keys);
        if (count($diff_keys)) {
            $remote->clearCache();
        }
    }
}
