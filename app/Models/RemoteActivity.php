<?php

namespace App\Models;

use App\Helpers\Utilities;
use App\Models\Enums\RemoteStatusType;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;



/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int remote_id
 * @property int caller_action_id
 * @property int caller_attribute_id
 * @property int caller_user_id
 * @property int caller_element_id
 * @property int caller_type_id
 * @property string ref_uuid
 * @property string remote_call_ended
 * @property RemoteStatusType status_type
 * @property int response_code
 * @property ArrayObject to_headers
 * @property ArrayObject from_headers
 * @property ArrayObject from_remote_processed_data
 * @property ArrayObject to_remote_processed_data
 * @property ArrayObject errors
 * @property string from_remote_raw_text
 *
 *
 *
 *
 * @property string created_at
 * @property string updated_at
 *
 * @property Remote remote_parent
 * @property Action caller_action
 * @property Attribute caller_attribute
 * @property User caller_user
 * @property Element caller_element
 * @property ElementType caller_type
 *
 */
class RemoteActivity extends Model
{

    protected $table = 'remote_activities';
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
        'to_headers' => ArrayObject::class,
        'from_headers' => ArrayObject::class,
        'from_remote_processed_data' => ArrayObject::class,
        'to_remote_processed_data' => ArrayObject::class,
        'errors' => ArrayObject::class,
        'status_type' => RemoteStatusType::class

    ];

    public function remote_parent() : BelongsTo {
        return $this->belongsTo('App\Models\Remote','remote_id');
    }

    public function caller_action() : BelongsTo {
        return $this->belongsTo('App\Models\Action','caller_action_id');
    }

    public function caller_attribute() : BelongsTo {
        return $this->belongsTo('App\Models\Attribute','caller_attribute_id');
    }

    public function caller_user() : BelongsTo {
        return $this->belongsTo('App\Models\User','caller_user_id');
    }

    public function caller_element() : BelongsTo {
        return $this->belongsTo('App\Models\Element','caller_element_id');
    }

    public function caller_type() : BelongsTo {
        return $this->belongsTo('App\Models\ElementType','caller_type_id');
    }

    public function addCache() : void {
        $older_cache = $this->remote_parent->getCache();

        $subkeys = [];
        foreach ($this->remote_parent->cache_keys as $some_key) {
            if (in_array($some_key,Remote::ALL_SPECIAL_CACHE_KEY_NAMES)) {
                $maybe = match($some_key) {
                  Remote::CACHE_KEY_NAME_ACTION => $this->caller_action?->ref_uuid,
                  Remote::CACHE_KEY_NAME_ATTRIBUTE => $this->caller_attribute?->ref_uuid,
                  Remote::CACHE_KEY_NAME_ELEMENT => $this->caller_element?->ref_uuid,
                  Remote::CACHE_KEY_NAME_TYPE => $this->caller_type?->ref_uuid,
                  Remote::CACHE_KEY_NAME_USER => $this->caller_user?->ref_uuid,
                };
                if ($maybe) {$subkeys[] = $maybe;}
            } else {
                $subkeys[] = $some_key;
            }
        }
        if (empty($subkeys)) {
            $subkey = Remote::CACHE_KEY_DEFAULT;
        } else {
            $subkey = implode('_',$subkeys);
        }
        $older_cache[$subkey] = $this->from_remote_processed_data??[];

        $final = Utilities::wrapJsonEncode($older_cache);
        Cache::tags([ Remote::REMOTES_CACHE_TAG])
            ->put($this->remote_parent->getCacheKey(), $final,$this->cache_ttl_seconds);
    }

}
