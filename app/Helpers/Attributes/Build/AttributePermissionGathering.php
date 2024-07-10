<?php

namespace App\Helpers\Attributes\Build;

use App\Models\Attribute;
use App\Models\AttributeUserGroupLookup;
use App\Models\Enums\Attributes\AttributeUserGroupType;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AttributePermissionGathering
{

    /**
     * @var AttributeUserGroupLookup[] $permission_groups
     */
    public array $permission_groups = [];


    public function __construct(Request $request)
    {
        $write_block = new Collection();
        $read_block = new Collection();
        $usage_block = new Collection();
        if ($request->request->has('permissions')) {
            $permissions_block = $request->collect('permissions');
            if ($permissions_block->has('user_groups')) {
                $groups_block = collect($permissions_block->get('user_groups'));
                if ($groups_block->has('read')) {
                    $read_block = collect($groups_block->get('read'));
                }
                if ($groups_block->has('write')) {
                    $write_block = collect($groups_block->get('write'));
                }
                if ($groups_block->has('usage')) {
                    $usage_block = collect($groups_block->get('usage'));
                }
            }
        }

        foreach ($write_block as $what) {
            $this->permission_groups[] = AttributeUserGroupLookup::createUserGroup($what,AttributeUserGroupType::WRITE);
        }
        foreach ($read_block as $what) {
            $this->permission_groups[] = AttributeUserGroupLookup::createUserGroup($what,AttributeUserGroupType::READ);
        }
        foreach ($usage_block as $what) {
            $this->permission_groups[] = AttributeUserGroupLookup::createUserGroup($what,AttributeUserGroupType::USAGE);
        }


    }

    public function assign(Attribute $attribute) {
        try {
            DB::beginTransaction();
            /** @var AttributeUserGroupLookup $what */
            foreach ($this->permission_groups as $what) {
                $what->group_lookup_attribute_id = $attribute->id;
                if ($what->delete_mode) {
                    $what->deleteModeActivate();
                } else {
                    $what->save();
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            /** @noinspection PhpUnhandledExceptionInspection */
            throw $e;
        }
    }


}
