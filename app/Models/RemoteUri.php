<?php

namespace App\Models;



use App\Models\Enums\Remotes\RemoteToMapType;
use App\Models\Enums\Remotes\RemoteUriDataFormatType;
use App\Models\Enums\Remotes\RemoteUriMethod;
use App\Models\Enums\Remotes\RemoteUriRoleType;
use App\Models\Enums\Remotes\RemoteUriType;
use App\Models\Traits\TResourceCommon;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int remote_id
 * @property int uri_port
 * @property int total_calls_made
 * @property int total_errors

 * @property RemoteUriType uri_type
 * @property RemoteUriMethod uri_method_type
 * @property RemoteUriDataFormatType uri_to_remote_format
 * @property RemoteUriDataFormatType uri_from_remote_format
 * @property RemoteUriRoleType uri_role
 * @property string uri_string
 * @property bool is_sending_context_to_remote
 * //todo add in bool for can element owner set manual remote answer
 * //todo add db level check that manual cannot be used on anything but default role because do not monitor response from others
 *
 * @property string created_at
 * @property string updated_at
 *
 * @property Remote parent_remote
 * @property RemoteToMap[] rules_to_remote
 * @property RemoteFromMap[] rules_from_remote
 *
 *
 */
class RemoteUri extends Model
{
    use TResourceCommon;
    protected $table = 'remote_uris';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'uri_type' => RemoteUriType::class,
        'uri_method_type' => RemoteUriMethod::class,
        'uri_to_remote_format' => RemoteUriDataFormatType::class,
        'uri_from_remote_format' => RemoteUriDataFormatType::class,
        'uri_role' => RemoteUriRoleType::class,
    ];



    public function rules_to_remote() : BelongsTo {
        return $this->belongsTo('App\Models\RemoteToMap','remote_uri_id')
            ->select('*')
            ->selectRaw(" extract(epoch from  created_at) as created_at_ts,  extract(epoch from  updated_at) as updated_at_ts");
    }
    public function rules_from_remote() : BelongsTo {
        return $this->belongsTo('App\Models\RemoteFromMap','remote_uri_id')
            ->select('*')
            ->selectRaw(" extract(epoch from  created_at) as created_at_ts,  extract(epoch from  updated_at) as updated_at_ts");
    }

    public function parent_remote() : BelongsTo {
        return $this->belongsTo('App\Models\Remote','user_id');
    }

    public function updateGlobalStats(bool $b_error) :void {
        if ($b_error) {
            $this->update(['total_errors' => $this->total_errors + 1,'total_calls_made' => $this->total_calls_made + 1]);
        } else {
            $this->increment('total_calls_made');
        }
    }


    public function processToSend(Collection $collection,RemoteToMapType $filter) : array {
        $ret = [];
        $my_data = $collection->toArray();
        foreach ($this->rules_to_remote as $rule) {
            if ($rule->map_type !== $filter) { continue; }
            $pair = $rule->applyRuleToGiven($my_data);
            if (!empty($pair)) {
                $ret = array_merge($ret,$pair);
            }
        }
        return $ret;
    }

    public function processFromSend(Collection|array $what) : array {
        $ret = [];
        if (is_object($what) ){
            $my_data = $what->toArray();
        } else {
            $my_data = $what;
        }

        foreach ($this->rules_from_remote as $rule) {
            $pair = $rule->applyRuleToGiven($my_data);
            if (!empty($pair)) {
                $ret = array_merge($ret,$pair);
            }
        }
        return $ret;
    }


}
