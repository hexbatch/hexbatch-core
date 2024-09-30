<?php

namespace App\Models;

use App\Enums\Server\TypeOfServerStatus;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int server_type_id
 * @property int server_admin_user_type_id
 * @property int server_element_id
 * @property string ref_uuid
 * @property TypeOfServerStatus server_status
 * @property string server_domain
 * @property string status_change_at
 *
 *  @property string created_at
 *  @property string updated_at
 *
 */
class Server extends Model
{
    //todo add this server to here when making the standard attributes

    //get, list, are done by paths
    // edit means details in the element
    //read and write attributes regular data updates in the api

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
