<?php
namespace App\Models\Enums\Remotes;
enum RemoteStackCategoryType : string {

    case MAIN = 'main';
    case ON_SUCCESS = 'on_success';
    case ON_FAILURE = 'on_failure';
    case ON_ALWAYS = 'on_always';


}
