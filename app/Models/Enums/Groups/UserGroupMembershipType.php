<?php
namespace App\Models\Enums\Groups;
enum UserGroupMembershipType : string {
    case WORKING = 'working';
    case DEFINED = 'defined';
}
