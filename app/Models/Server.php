<?php

namespace App\Models;

use App\Enums\Server\TypeOfServerStatus;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Sys\Res\ISystemModel;
use App\Sys\Res\Servers\IServer;
use App\Sys\Res\Types\Stk\Root\Signal\Semaphore\MasterSemaphore;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Communication with elsewhere uses @uses MasterSemaphore
 */

/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int server_type_id
 * @property int owning_namespace_id
 * @property bool is_system
 * @property string ref_uuid
 * @property TypeOfServerStatus server_status
 * @property string server_domain
 * @property string server_url
 * @property string server_name
 * @property string server_access_token
 * @property string status_change_at
 * @property string access_token_expires_at
 *
 * @property UserNamespace owning_namespace
 * @property ElementType server_type
 *
 *  @property string created_at
 *  @property string updated_at
 *
 */
class Server extends Model implements IServer,ISystemModel
{

    /*
     * When transferring sets, element order (the entry order of the elements to the set table) is always preserved
     *
     * When transferring sets, will fail to transfer set if any of the types in the set are forbidden on the other server
     *
     * server ns are not initially owned, but can be assigned to a user later
     *
     * When an element has linked to a set, and that element owner ns is registered on the same server that set is being copied to,
     * then also transfer that element
     *
     * All elements transferred have the same uuid in the newly created elements
     * All ns transferred keep their uuid
     * all incoming servers keep their uuid
     *
     * The server has a ns, any element stuff is in its public or private element
     */

    //get, list, are done by paths
    // edit means details in the element
    //read and write attributes regular data updates in the api

    //only namespaces are transferred not users themselves

    //live types on elements are transferred

    //copied elements are always copied inside a set, this set can be a type already known, or a container
    //when previously copied element is sent back to this server, that is updated via the attribute's merge policy for this re-import (different from live merge)


    /*
     note: when ns is transferred to elsewhere, the public set from the ns is sent too, minus any attributes or types not fitting into the access level
      if something needs to be updated, then rules can be made to push that element again
     */




    protected $table = 'servers';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

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
        'server_status' => TypeOfServerStatus::class,
    ];

    public function owning_namespace() : BelongsTo {
        return $this->belongsTo(UserNamespace::class,'owning_namespace_id');
    }

    public function server_type() : BelongsTo {
        return $this->belongsTo(ElementType::class,'server_type_id');
    }


    public static function buildServer(
        ?int            $me_id = null,
        ?int            $type_id = null,
        ?string         $uuid = null,
        ?bool           $is_system = null,
        ?string           $server_name = null,
    )
    : Builder
    {

        /**
         * @var Builder $build
         */
        $build = Server::select('servers.*');

        if ($me_id) {
            $build->where('servers.id', $me_id);
        }

        if ($type_id) {
            $build->where('servers.server_type_id', $type_id);
        }

        if ($uuid) {
            $build->where('servers.ref_uuid', $uuid);
        }

        if ($server_name) {
            $build->where('servers.server_name', $server_name);
        }

        if ($is_system !== null) {
            $build->where('is_system',$is_system);
        }

        /**
         * @uses Server::owning_namespace()
         */
        $build->with('owning_namespace');

        return $build;
    }

    public static function resolveServer(string $value, bool $throw_exception = true) : ?Server {
        $build = null;
        if (Utilities::is_uuid($value)) {
            $build = static::buildServer(uuid: $value);
        } else {
            $parts = explode(UserNamespace::NAMESPACE_SEPERATOR, $value);

            if (count($parts) === 1) {
                //by name
                $ns_name = $parts[0];
                $build = static::buildServer(server_name: $ns_name);

            }
        }
        $server = $build?->first();
        if (empty($server) && $throw_exception) {
            throw new HexbatchNotFound(
                __('msg.server_not_found',['ref'=>$value]),
                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                RefCodes::SERVER_NOT_FOUND
            );
        }

        return $server;
    }
    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return static::resolveServer($value);
    }

    public static function getThisServer(
        ?int             $id = null,
        ?int             $type_id = null,
        ?string          $uuid = null
    )
    : Server
    {
        $ret = static::buildServer(me_id:$id,type_id: $type_id,uuid: $uuid)->first();

        if (!$ret) {
            $arg_types = []; $arg_vals = [];
            if ($id) { $arg_types[] = 'id'; $arg_vals[] = $id;}
            if ($uuid) { $arg_types[] = 'uuid'; $arg_vals[] = $uuid;}
            if ($type_id) { $arg_types[] = 'type_id'; $arg_vals[] = $type_id;}
            $arg_val = implode('|',$arg_vals); $arg_type = implode('|',$arg_types);
            throw new HexbatchNotFound(
                __('msg.server_not_found_by',['types'=>$arg_type,'values'=>$arg_val]),
                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                RefCodes::SERVER_NOT_FOUND
            );
        }
        return $ret;
    }

    public function getName() :string {
        return $this->server_domain;
    }




    public function getServerObject(): ?Server
    {
        return $this;
    }

    public function getUuid(): string{
        return $this->ref_uuid;
    }

    protected static ?Server $default_server = null;
    public static function getDefaultServer(bool $b_throw_on_missing = true) : ?Server {
        if (static::$default_server) { return static::$default_server;}

        $server = Server::buildServer(is_system: true)->first();
        if (!$server && $b_throw_on_missing) {
            throw new \LogicException("No system server made");
        }
        return static::$default_server = $server;
    }


}
