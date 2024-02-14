<?php

namespace App\Helpers\Attributes\Apply;

class StandardAttributes
{
    const DEF = [
      [
          'name'=>'standard',
          'uuid'=>'6ac886fb-d52f-46fa-b5db-a3d0a91e0b85',
          'internal_description'=>'Base attribute for all standard attributes',
          'parent_uuid'=>null,
          'validator'=> null
      ],

        //standard info
        [
          'name'=>'standard.info',
          'uuid'=>'016b2926-ab06-44af-b1c5-81520b39975b',
          'internal_description'=>'Attributes having to do with describing',
          'parent_uuid'=>'6ac886fb-d52f-46fa-b5db-a3d0a91e0b85',
          'validator'=> null
      ], [
          'name'=>'standard.info.name',
          'uuid'=>'09963b71-e8e9-41f3-b58f-95ea4c51fc38',
          'internal_description'=>'A name not restricted to the naming rules of the code',
          'parent_uuid'=>'016b2926-ab06-44af-b1c5-81520b39975b',
          'validator'=> null
      ], [
          'name'=>'standard.info.email',
          'uuid'=>'e3b2254c-3ebd-44c3-9cfb-406560a12880',
          'internal_description'=>'vaguely resembles email',
          'parent_uuid'=>'016b2926-ab06-44af-b1c5-81520b39975b',
          'validator'=> [StandardAttributes::class,'validateEmail']
      ], [
          'name'=>'standard.info.phone_number',
          'uuid'=>'620b02d6-a9d6-4159-83a4-fd926688bcac',
          'internal_description'=>'e.164 only',
          'parent_uuid'=>'016b2926-ab06-44af-b1c5-81520b39975b',
          'validator'=> [StandardAttributes::class,'validatePhone']
      ], [
          'name'=>'standard.info.address',
          'uuid'=>'2b4b34f5-606f-47c0-8797-80f8ec38aed5',
          'internal_description'=>'any string',
          'parent_uuid'=>'016b2926-ab06-44af-b1c5-81520b39975b',
          'validator'=> null
      ], [
          'name'=>'standard.info.map_location',
          'uuid'=>'de7c7ae8-1d8a-4c4a-a0ec-ba80ca21c980',
          'internal_description'=>'2d map location',
          'parent_uuid'=>'016b2926-ab06-44af-b1c5-81520b39975b',
          'validator'=> [StandardAttributes::class,'validateMapLocation']
      ], [
          'name'=>'standard.info.timezone',
          'uuid'=>'5a188757-81a7-4dc5-87de-a08341a12c91',
          'internal_description'=>'name of the timezone',
          'parent_uuid'=>'016b2926-ab06-44af-b1c5-81520b39975b',
          'validator'=> [StandardAttributes::class,'validateTimezone']
      ], [
          'name'=>'standard.info.description',
          'uuid'=>'152c4fd4-d721-45c2-a75b-96d69992ef89',
          'internal_description'=>'any length text, or markdown or xml or json',
          'parent_uuid'=>'016b2926-ab06-44af-b1c5-81520b39975b',
          'validator'=> null
      ],

        //standard display
        [
            'name'=>'standard.display',
            'uuid'=>'d17ae25b-cc99-4ac2-a773-b51eddb95dc1',
            'internal_description'=>'Attributes having to do with display',
            'parent_uuid'=>'6ac886fb-d52f-46fa-b5db-a3d0a91e0b85',
            'validator'=> null
      ], [
          'name'=>'standard.display.primary_color',
          'uuid'=>'c84278fc-a1be-4dc7-b47b-a24bab2464db',
          'internal_description'=>'Primary color',
          'parent_uuid'=>'d17ae25b-cc99-4ac2-a773-b51eddb95dc1',
          'validator'=> [StandardAttributes::class,'validateColor']
      ], [
          'name'=>'standard.display.secondary_color',
          'uuid'=>'cfc6feb5-d92b-4db0-8146-058aee99a654',
          'internal_description'=>'Secondary color',
          'parent_uuid'=>'d17ae25b-cc99-4ac2-a773-b51eddb95dc1',
          'validator'=> [StandardAttributes::class,'validateColor']
      ], [
          'name'=>'standard.display.bg_color',
          'uuid'=>'3b39ce42-a37f-45ef-9972-21b0592e0f87',
          'internal_description'=>'Background color',
          'parent_uuid'=>'d17ae25b-cc99-4ac2-a773-b51eddb95dc1',
          'validator'=> [StandardAttributes::class,'validateColor']
      ],[
          'name'=>'standard.display.opacity',
          'uuid'=>'8a569174-35a8-4af9-8531-1b07c135c63c',
          'internal_description'=>'Opacity',
          'parent_uuid'=>'d17ae25b-cc99-4ac2-a773-b51eddb95dc1',
          'validator'=> [StandardAttributes::class,'validateOpacity']
      ],[
          'name'=>'standard.display.svg',
          'uuid'=>'f5baab77-66bb-4ccc-917f-6f36afc70051',
          'internal_description'=>'svg',
          'parent_uuid'=>'d17ae25b-cc99-4ac2-a773-b51eddb95dc1',
          'validator'=> [StandardAttributes::class,'validateSvg']
      ],[
          'name'=>'standard.display.image',
          'uuid'=>'2fa8ff2b-1735-4497-b7e0-83645e42240f',
          'internal_description'=>'url to image',
          'parent_uuid'=>'d17ae25b-cc99-4ac2-a773-b51eddb95dc1',
          'validator'=> [StandardAttributes::class,'validateUrl']
      ],[
          'name'=>'standard.display.small_thumbnail',
          'uuid'=>'43e9362d-2bf1-409e-9b87-cc29bf95560c',
          'internal_description'=>'url to image',
          'parent_uuid'=>'d17ae25b-cc99-4ac2-a773-b51eddb95dc1',
          'validator'=> [StandardAttributes::class,'validateUrl']
      ],[
          'name'=>'standard.display.medium_thumbnail',
          'uuid'=>'fbfff4ce-410f-42a0-9e99-87963b131446',
          'internal_description'=>'url to image',
          'parent_uuid'=>'d17ae25b-cc99-4ac2-a773-b51eddb95dc1',
          'validator'=> [StandardAttributes::class,'validateUrl']
      ],
        //category
        [
          'name'=>'standard.category',
          'uuid'=>'09dc6180-f48a-4acb-9668-67e08a6d5ea3',
          'internal_description'=>'Base for all standard categories',
          'parent_uuid'=>'6ac886fb-d52f-46fa-b5db-a3d0a91e0b85',
          'validator'=> null
      ],[
            'name'=>'standard.category.remote',
            'uuid'=>'618689d9-6dd2-40dc-a288-443d050c71bb',
            'internal_description'=>'Remote Category',
            'parent_uuid'=>'09dc6180-f48a-4acb-9668-67e08a6d5ea3',
            'validator'=> null
        ],[
            'name'=>'standard.category.group',
            'uuid'=>'f83aa255-862a-413f-8d9c-083d21c9d983',
            'internal_description'=>'User Group Category',
            'parent_uuid'=>'09dc6180-f48a-4acb-9668-67e08a6d5ea3',
            'validator'=> null
        ],[
            'name'=>'standard.category.user',
            'uuid'=>'048a58ef-374c-4369-bc9a-fd04210d66a4',
            'internal_description'=>'User category',
            'parent_uuid'=>'09dc6180-f48a-4acb-9668-67e08a6d5ea3',
            'validator'=> null
        ],


        //admin roles
        [
            'name'=>'standard.admin_role',
            'uuid'=>'61370c98-57c5-4b2d-a64e-6d9fa336191b',
            'internal_description'=>'Base for all standard admin roles',
            'parent_uuid'=>'6ac886fb-d52f-46fa-b5db-a3d0a91e0b85',
            'validator'=> null
        ],
        [
            'name'=>'standard.admin_role.view_private_user_info',
            'uuid'=>'be806efe-fffb-4a39-a9ad-6b1b705c2fb1',
            'internal_description'=>'Users with this attribute can view all other user info',
            'parent_uuid'=>'61370c98-57c5-4b2d-a64e-6d9fa336191b',
            'validator'=> null
        ],[
            'name'=>'standard.admin_role.set_sensitive_remote_types',
            'uuid'=>'a202179c-a8af-4f38-a612-7ddb719d4012',
            'internal_description'=>'Users with this attribute can set all the remote types',
            'parent_uuid'=>'61370c98-57c5-4b2d-a64e-6d9fa336191b',
            'validator'=> null
        ],[
            'name'=>'standard.admin_role.view_all_remote_activity',
            'uuid'=>'63b649da-2a3f-4940-8f78-ad8ac3109443',
            'internal_description'=>'Users with this attribute can see all remote activity by all users',
            'parent_uuid'=>'61370c98-57c5-4b2d-a64e-6d9fa336191b',
            'validator'=> null
        ],

        //set relationships
        [
            'name'=>'standard.set_relation',
            'uuid'=>'25197025-96e4-46dc-8b41-433a6336dc9d',
            'internal_description'=>'Base for all standard set relationships',
            'parent_uuid'=>'6ac886fb-d52f-46fa-b5db-a3d0a91e0b85',
            'validator'=> null
        ],

        //set types
        [
            'name'=>'standard.set_relation',
            'uuid'=>'1ea12c69-6fed-4fa7-a816-73326a222b82',
            'internal_description'=>'Base for all standard set types',
            'parent_uuid'=>'6ac886fb-d52f-46fa-b5db-a3d0a91e0b85',
            'validator'=> null
        ],


    ];

    public static function validateEmail($what) : void {

    }
    public static function validatePhone($what) : void {

    }
    public static function validateMapLocation($what) : void {

    }
    public static function validateTimezone($what) : void {

    }
    public static function validateUrl($what) : void {

    }
    public static function validateSvg($what) : void {

    }
    public static function validateOpacity($what) : void {

    }
    public static function validateColor($what) : void {

    }
}
