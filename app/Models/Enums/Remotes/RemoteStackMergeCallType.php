<?php
namespace App\Models\Enums\Remotes;
enum RemoteStackMergeCallType : string {

    case ALL_MUST_SUCCEED = 'all_must_succeed';
    case SOME_FAILING_OK = 'some_failing_ok';

}
