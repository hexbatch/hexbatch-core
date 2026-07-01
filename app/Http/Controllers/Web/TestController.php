<?php

namespace App\Http\Controllers\Web;



use App\Models\Attribute;
use App\Models\ElementType;
use JetBrains\PhpStorm\NoReturn;

class TestController
{

    #[NoReturn]
    public function test() {

        $tests = [
            73,
            '74fd4412-52f3-4c82-820a-2bff3f5f7933',
            'will_system:content:content_body',
            'server_one:will_system:content:content_data',
            'server_one:d5eb0c72-1db8-4658-9615-3502ef724e51:c9f9671e-905f-4198-9049-ef0e1ad4d268:content_data',
            100,
            'content_body'
        ];

        $what = Attribute::getAttributeIdsFromInput(
         $tests,
        default_type: 'content',
        default_ns: 'will_system',
        );
        dd($what);

//        $what = ElementType::getTypeIdsFromInput(references: [
//            '92b4dafb-5240-4d7f-8d8a-f069cc79cec2',
//             'server_one:will:will_private',
//            '32998341-aa98-425e-b556-c342d029bb56:6bd813d7-f3e7-4d72-9fbc-1a5dfa26e158:29fd4271-7f0c-47f8-ae95-fed83e48d9a9',
//             559,
//            'will_public',
//            'bdb74bcc-bb1c-4f29-b33c-81c06e0b52f0',
//            'server_one:will3:will3_public',
//            'server_one:will_system:event_scope_set',
//            26
//        ],default_ns: '6bd813d7-f3e7-4d72-9fbc-1a5dfa26e158');
//        dd($what);
    }
}
