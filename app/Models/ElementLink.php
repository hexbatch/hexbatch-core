<?php

namespace App\Models;


use App\Exceptions\HexbatchNotFound;
use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Symfony\Component\HttpFoundation\Response as CodeOf;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int linker_element_id
 * @property int link_to_set_id
 * @property string ref_uuid
 *
 * @property string created_at
 * @property string updated_at
 *
 * @property Element linking_element
 * @property ElementSet linked_set
 */
class ElementLink extends Model
{

    protected $table = 'element_links';
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

    public function linking_element() : BelongsTo {
        return $this->belongsTo(Element::class,'linker_element_id');
    }

    public function linked_set() : BelongsTo {
        return $this->belongsTo(ElementSet::class,'link_to_set_id');
    }

    public static function buildLink(
        ?int            $me_id = null,
        ?string         $uuid = null,
        ?int         $linking_element_id = null,
        ?int         $linked_set_id = null,
        bool         $with_linker_element = false,
        bool         $with_linked_set = false,
    )
    : Builder
    {

        /**
         * @var Builder $build
         */
        $build = ElementSet::select('element_links.*')
            ->selectRaw(" extract(epoch from  element_links.created_at) as created_at_ts")
            ->selectRaw("  extract(epoch from  element_links.updated_at) as updated_at_ts")
        ;

        if ($me_id) {
            $build->where('element_links.id', $me_id);
        }

        if ($uuid) {
            $build->where('element_links.ref_uuid', $uuid);
        }

        if ($linking_element_id) {
            $build->where('element_links.linker_element_id', $linking_element_id);
        }

        if ($linked_set_id) {
            $build->where('element_links.link_to_set_id', $linked_set_id);
        }

        if ($with_linked_set) {
            /** @uses static::linked_set() */
            $build->with('linked_set');
        }

        if ($with_linker_element) {
            /** @uses static::linking_element()*/
            $build->with('linking_element');
        }



        return $build;
    }

    public static function makeLink(Element $el,ElementSet $set) : static {

        $maybe_exists = static::buildLink(linking_element_id: $el->id,linked_set_id: $set->id)->first();
        if ($maybe_exists) {return $maybe_exists;}

        if ($el->element_phase_id !== $set->defining_element->element_phase_id) {
            throw new HexbatchNotPossibleException(
                __("msg.set_has_different_phase_than_linking_element"
                    ,   [
                        'set_phase'=>$set->defining_element->element_phase->getName(),
                        'other_phase'=>$el->defining_element->element_phase->getName(),
                    ]
                ),
                CodeOf::HTTP_NOT_FOUND,
                RefCodes::PHASE_IS_DIFFERENT);
        }

        $node = new ElementLink();
        $node->link_to_set_id = $set->id;
        $node->linker_element_id = $el->id;
        $node->save();
        $node->refresh();
        return $node;
    }

    public static function destroyLink(Element $el,ElementSet $set) : ?static {

        /** @var static|null $maybe_exists */
        $maybe_exists = static::buildLink(linking_element_id: $el->id,linked_set_id: $set->id)->first();
         $maybe_exists?->delete();
        return $maybe_exists;

    }


    public static function getThisLink(
        ?int             $id = null,
        ?string          $uuid = null
    )
    : ElementLink
    {
        $ret = static::buildLink(me_id:$id,uuid: $uuid)->first();

        if (!$ret) {
            $arg_types = []; $arg_vals = [];
            if ($id) { $arg_types[] = 'id'; $arg_vals[] = $id;}
            if ($uuid) { $arg_types[] = 'uuid'; $arg_vals[] = $uuid;}
            $arg_val = implode('|',$arg_vals);
            $arg_type = implode('|',$arg_types);
            throw new HexbatchNotFound(
                __('msg.link_not_found_by',['types'=>$arg_type,'values'=>$arg_val]),
                CodeOf::HTTP_NOT_FOUND,
                RefCodes::LINK_NOT_FOUND
            );
        }
        return $ret;
    }

    public static function resolveLink(string $value, bool $throw_exception = true)
    : static
    {

        /** @var Builder $build */
        $build = null;

        if (Utilities::is_uuid($value)) {
            return static::getThisLink(uuid: $value);
        }

        $ret = $build?->first();

        if (empty($ret) && $throw_exception) {
            throw new HexbatchNotFound(
                __('msg.link_not_found',['ref'=>$value]),
                CodeOf::HTTP_NOT_FOUND,
                RefCodes::LINK_NOT_FOUND
            );
        }

        return $ret;
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
        return static::resolveLink($value);

    }

}
