<?php

namespace App\Models;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\Enums\Bounds\LocationType;
use App\Models\Traits\TResourceCommon;
use ArrayObject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use ResourceBundle;

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int parent_remote_id
 * @property int remote_time_bounds_id
 * @property int remote_map_bounds_id
 * @property ArrayObject remote_icu_locale_codes
 * @property string remote_terms_of_use_link
 * @property string remote_privacy_link
 * @property string remote_about_link
 * @property string remote_description

 *
 * @property string created_at
 * @property string updated_at
 *
 * @property TimeBound remote_meta_time_bound
 * @property LocationBound remote_meta_map_bound
 *
 */
class RemoteMetum extends Model
{
    use TResourceCommon;

    protected $table = 'remote_meta';
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
        'remote_icu_locale_codes' => AsArrayObject::class,
    ];

    const META_VALUE_MAX_LENGTH = 255;

    public function remote_meta_time_bound() : BelongsTo {
        return $this->belongsTo('App\Models\TimeBound','remote_time_bounds_id')
            ->select('*')
            ->selectRaw(" extract(epoch from  bound_start) as bound_start_ts,  extract(epoch from  bound_stop) as bound_stop_ts");
    }

    public function remote_meta_map_bound() : BelongsTo {
        return $this->belongsTo('App\Models\LocationBound','remote_map_bounds_id')
            ->select('*')
            ->selectRaw(" extract(epoch from  created_at) as created_at_ts,  extract(epoch from  updated_at) as updated_at_ts,ST_AsGeoJSON(geom) as geom_as_geo_json");
    }

    public static function createMetum(Collection $c,?Remote $parent = null) : RemoteMetum {

        $ret = new RemoteMetum();
        if ($parent) {$ret->parent_remote_id = $parent->id;}
        if ($c->has('time_bounds')) {
            $time_hint = $c->get('time_bounds');
            if (!(empty($time_hint) || is_string($time_hint) ) ) {
                throw new HexbatchNotPossibleException(__("msg.remote_schema_meta_empty_meta"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::REMOTE_SCHEMA_ISSUE);
            }
            if ($time_hint) {
                /**
                 * @var TimeBound $time_bound
                 */
                $time_bound = (new TimeBound())->resolveRouteBinding($time_hint);
                if (!$time_bound->bound_owner->user_group->isAdmin(Auth::id())) {
                    throw new HexbatchNotPossibleException(__("msg.remote_schema_meta_bounds_admin_group", ['ref' => $time_bound->getName()]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::REMOTE_SCHEMA_ISSUE);
                }
                $ret->remote_time_bounds_id = $time_bound->id;
            } else {
                $ret->remote_time_bounds_id = null;
            }
        }

        if ($c->has('map_bounds')) {
            $map_hint = $c->get('map_bounds');
            if (!(empty($map_hint) || is_string($map_hint) )) {
                throw new HexbatchNotPossibleException(__("msg.remote_schema_meta_empty_meta"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::REMOTE_SCHEMA_ISSUE);
            }
            if ($map_hint) {
                /**
                 * @var LocationBound $map_bound
                 */
                $map_bound = (new LocationBound())->resolveRouteBinding($map_hint);
                if (!$map_bound->bound_owner->user_group->isAdmin(Auth::id())) {
                    throw new HexbatchNotPossibleException(__("msg.remote_schema_meta_bounds_admin_group", ['ref' => $map_bound->getName()]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::REMOTE_SCHEMA_ISSUE);
                }
                if ($map_bound->location_type !== LocationType::MAP) {
                    throw new HexbatchNotPossibleException(__("msg.remote_schema_meta_map_wrong_type", ['ref' => $map_bound->getName()]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::REMOTE_SCHEMA_ISSUE);
                }
                $ret->remote_map_bounds_id = $map_bound->id;
            } else {
                $ret->remote_map_bounds_id = null;
            }
        }
        $ret->set_string_meta_value($c,'privacy_link','remote_privacy_link',static::META_VALUE_MAX_LENGTH);
        $ret->set_string_meta_value($c,'terms_of_use_link','remote_terms_of_use_link',static::META_VALUE_MAX_LENGTH);
        $ret->set_string_meta_value($c,'about_link','remote_about_link',static::META_VALUE_MAX_LENGTH);
        $ret->set_string_meta_value($c,'description','remote_description');

        $locale_codes = ResourceBundle::getLocales('');
        //remote_icu_locale_codes
        if ($c->has('icu_locale_codes')) {
            $codes_raw = $c->get('icu_locale_codes');
            if (!is_array($codes_raw)) {
                $codes_raw = [$codes_raw];
            }
            foreach ($codes_raw as $maybe_lang_code) {
                if (empty($maybe_lang_code)) {continue;}
                if (in_array($maybe_lang_code,$locale_codes)) {
                    $ret->remote_icu_locale_codes[] = $maybe_lang_code;
                }
            }
        }

        return $ret;
    }

    protected function set_string_meta_value(Collection $c,string $lookup_name,string $field_name,?int $max_length = null) {
        if ($c->has($lookup_name)) {
            $maybe_value = $c->get($lookup_name);
            if (!is_string($maybe_value) ) {
                throw new HexbatchNotPossibleException(__("msg.remote_schema_meta_empty_meta"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::REMOTE_SCHEMA_ISSUE);
            }

            if($max_length && mb_strlen($maybe_value) >= $max_length ) {
                throw new HexbatchNotPossibleException(__("msg.remote_schema_meta_empty_meta",['ref'=>$lookup_name]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::REMOTE_SCHEMA_ISSUE);
            }
            $processed = Utilities::strip_tags_convert_entities($maybe_value);
            if (empty($processed)) {$processed = null;}
            $this->$field_name = $processed;
        } else {
            $this->$field_name = null;
        }
    }

}
