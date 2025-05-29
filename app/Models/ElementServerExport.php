<?php

namespace App\Models;



use App\Sys\Res\Types\Stk\Root\TrackingExported;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


/**
 *
 * set one row with type and null element for each type exported, this way can remember type sent when all elements sent are deleted
 * elements only put on this table if it inherits from @see TrackingExported
 *
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int export_server_id
 * @property int export_type_id
 * @property bool export_element_id
 *
 *
 */
class ElementServerExport extends Model
{

    protected $table = 'element_server_exports';
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
    protected $casts = [];

}
