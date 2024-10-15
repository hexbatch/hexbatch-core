<?php

namespace App\Models;

use App\Enums\Server\TypeOfServerStatus;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Sys\Res\Namespaces\INamespace;
use App\Sys\Res\Servers\IServer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int server_type_id
 * @property int owning_namespace_id
 * @property string ref_uuid
 * @property TypeOfServerStatus server_status
 * @property string server_domain
 * @property string server_name
 * @property string status_change_at
 *
 * @property UserNamespace owning_namespace
 *
 *  @property string created_at
 *  @property string updated_at
 *
 */
class Server extends Model implements IServer
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


    public static function buildServer(
        ?int $id = null)
    : Builder
    {

        /**
         * @var Builder $build
         */
        $build = Element::select('servers.*');

        if ($id) {
            $build->where('servers.id', $id);
        }

        /**
         * @uses Server::owning_namespace()
         */
        $build->with('owning_namespace');

        return $build;
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
        $build = null;
        $ret = null;
        $first_id = null;
        try {
            if ($field) {
                $build = $this->where($field, $value);
            } else {
                if (Utilities::is_uuid($value)) {
                    $build = $this->where('ref_uuid', $value);
                } else {
                    if (is_string($value)) {
                        $parts = explode(UserNamespace::NAMESPACE_SEPERATOR, $value);
                        if (count($parts) === 1) {
                            $s_name = $parts[0];
                            $build = $this->where('server_name', $s_name);
                        }
                    }
                }
            }
            if ($build) {
                $first_id = (int)$build->value('id');
                if ($first_id) {
                    $ret = Server::buildServer(id:$first_id)->first();
                }
            }
        } finally {
            if (empty($ret) || empty($first_id) || empty($build)) {
                throw new HexbatchNotFound(
                    __('msg.server_not_found',['ref'=>$value]),
                    \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                    RefCodes::SERVER_NOT_FOUND
                );
            }
        }
        return $ret;

    }

    public function getName() :string {
        return $this->id;
    }

    public function getServerUuid(): string
    {
        return $this->ref_uuid;
    }

    public function getServerDomain(): string
    {
       return $this->server_domain;
    }

    public function getServerName(): string
    {
        return $this->getName();
    }

    public function getServerNamespaceInterface(): ?INamespace
    {
        return $this->owning_namespace;
    }

    public function getServerObject(): ?Server
    {
        return $this;
    }
}
