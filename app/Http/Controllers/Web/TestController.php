<?php

namespace App\Http\Controllers\Web;



use App\Models\ElementType;
use App\Models\ElementTypeAncestor;
use App\Models\ElementTypeExposedAttribute;
use App\Sys\Build\SystemResources;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ele\LinkAdd;
use App\Sys\Res\Types\Stk\Root\Evt\Server\LinkCreated;

class TestController
{

    public function test() {
        dd(ElementType::getElementType(id:321)->getAncestorsAsFlat()->toArray());
        $event = new LinkCreated(b_type_init: true);
        if ($event instanceof (LinkAdd::getEventClass()) ) {
            print "works";
        } else {
            print "fail";
        }
        exit;
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
