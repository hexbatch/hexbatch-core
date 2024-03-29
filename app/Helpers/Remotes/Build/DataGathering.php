<?php

namespace App\Helpers\Remotes\Build;

use App\Models\Remote;
use App\Models\RemoteFromMap;
use App\Models\RemoteToMap;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DataGathering
{
    /**
     * @var RemoteToMap[] $to_map
     */
    public array $to_map = [];

    /**
     * @var RemoteFromMap[] $from_map
     */
    public array $from_map = [];



    public function __construct(Request $request)
    {


        $data_block = new Collection();
        $from_block = new Collection();
        $to_block = new Collection();

        if ($request->request->has('data')) {
            $data_block = $request->collect('data');
        }
        if ($data_block->has('from_remote_map')) {
            $from_block = collect($data_block->get('from_remote_map'));
        } //end from_remote_map block

         if ($data_block->has('to_remote_map')) {
            $to_block = collect($data_block->get('to_remote_map'));
        } //end from_remote_map block

        foreach ($from_block as $some_from) {

            $this->from_map[] = RemoteFromMap::createMap(new Collection($some_from));
        }

        foreach ($to_block as $some_to) {
            $this->to_map[] = RemoteToMap::createMap(new Collection($some_to));
        }



    }

    public function assign(Remote $remote) {


        try {
            DB::beginTransaction();

            if (count($this->from_map)) {
                RemoteFromMap::where('parent_remote_id',$remote->id)->delete();
            }

            if (count($this->to_map)) {
                RemoteToMap::where('parent_remote_id',$remote->id)->delete();
            }
            /** @var RemoteFromMap $what */
            foreach ($this->from_map as $what) {
                $what->parent_remote_id = $remote->id;
                $what->save();
            }

            /** @var RemoteToMap $what */
            foreach ($this->to_map as $what) {
                $what->parent_remote_id = $remote->id;
                $what->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            /** @noinspection PhpUnhandledExceptionInspection */
            throw $e;
        }

    }
}
