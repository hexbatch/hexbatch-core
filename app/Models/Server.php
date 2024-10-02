<?php

namespace App\Models;

use App\Enums\Server\TypeOfServerStatus;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int server_type_id
 * @property int owning_namespace_id
 * @property int server_element_id
 * @property string ref_uuid
 * @property TypeOfServerStatus server_status
 * @property string server_domain
 * @property string server_name
 * @property string server_public_key
 * @property string status_change_at
 *
 *  @property string created_at
 *  @property string updated_at
 *
 */
class Server extends Model
{
    //todo add this server to here when making the standard attributes

    /*
     * When transferring sets, element order (the entry order of the elements to the set table) is always preserved
     *
     * When transferring sets, will fail to transfer set if any of the types in the set are forbidden on the other server
     *
     * server ns are not initially owned, but can be assigned to a user later
     */

    //get, list, are done by paths
    // edit means details in the element
    //read and write attributes regular data updates in the api

    //only namespaces are transferred not users themselves

    //live types on elements are transferred

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
}
