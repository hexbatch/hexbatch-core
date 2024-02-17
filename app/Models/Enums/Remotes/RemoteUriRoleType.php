<?php
namespace App\Models\Enums\Remotes;
enum RemoteUriRoleType : string
{

    case DEFAULT = 'default';
    case API_SUCCESS = 'api_success';
    case API_FAIL = 'api_fail';

}
