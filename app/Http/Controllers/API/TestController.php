<?php

namespace App\Http\Controllers\API;




use App\Sys\Build\SystemResources;
use Illuminate\Support\Facades\DB;

class TestController
{
    /**
     * @return void
     * @throws \Exception
     */
    public function test() {
        try {
            DB::beginTransaction();
            SystemResources::generateObjects();
            SystemResources::doNextSteps();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
