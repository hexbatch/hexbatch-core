<?php

namespace App\Http\Controllers\Web;



use App\Models\TimeBound;
use App\Models\TimeBoundSpan;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class TestController
{

    public function test() {
//        SystemResources::build();

        $now_ts = time();
        $my_unix = $now_ts + TimeBound::MAKE_PERIOD_SECONDS;

        $query = TimeBoundSpan::whereRaw("tstzrange( null,now() - interval '1 hour') &&  time_slice_range ")

        ;
        echo $query->toRawSql();

//        dd ($query->get()->toArray());
    }
}
