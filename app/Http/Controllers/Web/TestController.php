<?php

namespace App\Http\Controllers\Web;



use App\Models\ElementType;
use App\Models\ElementTypeAncestor;
use App\Models\ElementTypeExposedAttribute;
use App\Sys\Build\SystemResources;

class TestController
{

    public function test() {

       print "<pre>";
        ElementType::where('id','>',0)->chunk(100, function($records)  {
            /** @var ElementType $type */
            foreach ($records as $type) {





                $recs = ElementTypeExposedAttribute::makeRecords(type: $type);
                if (empty($recs)) {continue;}
                var_dump($recs);
            }
        });

    }
}
