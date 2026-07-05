<?php


namespace App\Sys\Res\Types;

use App\Data\ApiParams\Data\Elements\ElementData;
use App\Data\ApiParams\Data\Elements\Responses\ElementList;
use App\Helpers\Events\IEventReference;
use App\Helpers\Utilities;
use App\Models\Attribute;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\ElementType;
use App\Models\LocationBound;
use App\Models\Phase;
use App\Models\Server;
use App\Models\TimeBound;
use App\Models\UserNamespace;
use Illuminate\Support\Collection;
use Spatie\LaravelData\CursorPaginatedDataCollection;
use Spatie\LaravelData\DataCollection;
use TorMorten\Eventy\Facades\Eventy;

trait GetFromArrayTrait
{
    protected static function getNamespaceFromArray(string $array_key, array $source) : UserNamespace {
        if (!isset($source[$array_key])) {throw new \LogicException("No array key set namespace");}
        if ( ($found = $source[$array_key])  instanceof UserNamespace) {return $found;}
        $ref = null;
        if (is_array($found) && isset($found['ref_uuid'])) {$ref = $found['ref_uuid'];}
        if (!$ref && is_object($found) && property_exists($found,'ref_uuid')) {$ref = $found->ref_uuid;}
        if ($ref) {
            return UserNamespace::getThisNamespace(uuid: $ref );
        }
        throw new \LogicException("Could not find namespace in $array_key");
    }

    protected static function getPhaseFromArray(string $array_key, array $source) : Phase {
        if (!isset($source[$array_key])) {throw new \LogicException("No array key set for phase");}
        if ( ($found = $source[$array_key])  instanceof UserNamespace) {return $found;}
        $ref = null;
        if (is_array($found) && isset($found['ref_uuid'])) {$ref = $found['ref_uuid'];}
        if (!$ref && is_object($found) && property_exists($found,'ref_uuid')) {$ref = $found->ref_uuid;}
        if ($ref) {
            return Phase::getThisPhase(uuid: $ref );
        }
        throw new \LogicException("Could not find phase in $array_key");
    }

    protected static function getServerFromArray(string $array_key, array $source,bool $b_throw_exception = false) : ?Server {
        if (!isset($source[$array_key])) {
            if ($b_throw_exception) {
                throw new \LogicException("No array key set for server");
            } else {
                return null;
            }

        }
        if ( ($found = $source[$array_key])  instanceof Server) {return $found;}
        $ref = null;
        if (is_array($found) && isset($found['ref_uuid'])) {$ref = $found['ref_uuid'];}
        if (!$ref && is_object($found) && property_exists($found,'ref_uuid')) {$ref = $found->ref_uuid;}
        if ($ref) {
            return Server::getThisServer(uuid: $ref );
        }
        throw new \LogicException("Could not find server in $array_key");
    }



    protected static function getTypeFromArray(string $array_key, array $source) : ElementType {
        if (!isset($source[$array_key])) {throw new \LogicException("No array key set for type");}
        if ( ($found = $source[$array_key])  instanceof ElementType) {return $found;}
        $ref = null;
        if (is_array($found) && isset($found['ref_uuid'])) {$ref = $found['ref_uuid'];}
        if (!$ref && is_object($found) && property_exists($found,'ref_uuid')) {$ref = $found->ref_uuid;}
        if ($ref) {
            return ElementType::getElementType(uuid: $ref );
        }
        throw new \LogicException("Could not find element type in $array_key");
    }

    protected static function getElementFromArray(?string $array_key, array $source) : Element {
        if ($array_key)
        {
            if (!isset($source[$array_key])) {throw new \LogicException("No array key set for element");}
            if ( ($found = $source[$array_key])  instanceof Element) {return $found;}
        } else {
            $found = $source;
        }

        $ref = null;
        if (is_array($found) && isset($found['ref_uuid'])) {$ref = $found['ref_uuid'];}
        if (!$ref && is_object($found) && property_exists($found,'ref_uuid')) {$ref = $found->ref_uuid;}
        if ($ref) {
            return Element::getThisElement(uuid: $ref );
        }
        throw new \LogicException("Could not find element in $array_key");
    }

    protected static function getSetFromArray(?string $array_key, array $source,bool $throw_if_missing = true)
    : null|ElementSet
    {
        if ($array_key)
        {
            if (!isset($source[$array_key])) {
                if ($throw_if_missing)
                {
                    throw new \LogicException("No array key made for set");
                } else {
                    return null;
                }

            }
            if ( ($found = $source[$array_key])  instanceof ElementSet) {return $found;}
        } else {
            $found = $source;
        }

        $ref = null;
        if (is_array($found) && isset($found['ref_uuid'])) {$ref = $found['ref_uuid'];}
        if (!$ref && is_object($found) && property_exists($found,'ref_uuid')) {$ref = $found->ref_uuid;}
        if ($ref) {
            return ElementSet::getThisSet(uuid: $ref );
        }
        throw new \LogicException("Could not find set in $array_key");
    }

    /**
     * @return Collection<Element>|null
     */
    protected static function getElementCollectionFromArray(string $array_key, array $source,bool $throw_if_missing = true) : ?Collection {
        if (!isset($source[$array_key])) {throw new \LogicException("No array key set for collection");}
        /** @var Collection $found */
        if ( ($found = $source[$array_key])  instanceof Collection) {
            return $found;
        }
        //might be array of stuff that has ref
        if (is_array($found) || is_iterable($found)) {
            $els = [];
            $refs = [];
            foreach ($found as $el) {
                if ($el instanceof Element) { $els[] = $el;}
                elseif (is_string($el) && Utilities::is_uuid($el)) {
                    $refs[] = $el;
                }
            }

            $ret = collect($els);
            if (count($refs)) {
                $my_els = Element::buildElement(given_uuids: $refs, b_do_namespace_relation: true, b_do_namespace_type_relation: true, b_do_type_relation: true)->get();
                $ret->concat($my_els->toArray());
            }


            return collect($ret);
        }
        if ($throw_if_missing) {
            throw new \LogicException("Could not find element collection in $array_key");
        }
        return null;

    }

    /**
     * @return Collection<IEventReference>|null
     */
    protected static function getEventCollectionFromArray(string $array_key, array $source,bool $throw_if_missing = true) : ?Collection {
        if (!isset($source[$array_key])) {throw new \LogicException("No array key set for collection");}
        /** @var Collection $found */
        if ( ($found = $source[$array_key])  instanceof Collection) {
            return $found;
        }
        //might be array of stuff that has ref
        if (is_array($found) || is_iterable($found)) {
            $els = [];
            $refs = [];
            foreach ($found as $el) {
                if ($el instanceof Element) { $els[] = $el;}
                elseif (is_string($el) && Utilities::is_uuid($el)) {
                    $refs[] = $el;
                }
            }

            $ret = collect($els);
            if (count($refs)) {
                $my_evs = Eventy::filter('IEventsFromReferences', $refs);
                $ret->concat($my_evs->toArray());
            }


            return collect($ret);
        }
        if ($throw_if_missing) {
            throw new \LogicException("Could not find event reference collection in $array_key");
        }
        return null;

    }


    protected static function getCollectionFromArray(string $array_key, array $source,bool $throw_if_missing = true) : ?Collection {
        if (!isset($source[$array_key])) {throw new \LogicException("No array key set for generic collection");}
        /** @var Collection $found */
        if ( ($found = $source[$array_key])  instanceof Collection) {
            return $found;
        }
        //might be array of stuff that has ref
        if (is_array($found) || is_iterable($found)) {
            return collect($found);
        }
        if ($throw_if_missing)
        {
            throw new \LogicException("Could not find collection in $array_key");
        }

        return null;
    }

    protected static function getLocationFromArray(string $array_key, array $source) : ?LocationBound {
        if (!isset($source[$array_key])) {throw new \LogicException("No array key set for location");}
        if ( ($found = $source[$array_key])  instanceof LocationBound) {return $found;}
        $ref = null;
        if (is_array($found) && isset($found['ref_uuid'])) {$ref = $found['ref_uuid'];}
        if (!$ref && is_object($found) && property_exists($found,'ref_uuid')) {$ref = $found->ref_uuid;}
        if ($ref) {
            return LocationBound::getThisLocation(uuid: $ref );
        }
        throw new \LogicException("Could not find location bound in $array_key");
    }

    protected static function getScheduleFromArray(string $array_key, array $source) : ?TimeBound {
        if (!isset($source[$array_key])) {throw new \LogicException("No array key set for schedule");}
        if ( ($found = $source[$array_key])  instanceof TimeBound) {return $found;}
        $ref = null;
        if (is_array($found) && isset($found['ref_uuid'])) {$ref = $found['ref_uuid'];}
        if (!$ref && is_object($found) && property_exists($found,'ref_uuid')) {$ref = $found->ref_uuid;}
        if ($ref) {
            return TimeBound::getThisSchedule(uuid: $ref );
        }
        throw new \LogicException("Could not find schedule bound in $array_key");
    }

    protected static function getAttributeFromArray(string $array_key, array $source) : ?Attribute {
        if (!isset($source[$array_key])) {throw new \LogicException("No array key set for attribute");}
        if ( ($found = $source[$array_key])  instanceof Attribute) {return $found;}
        $ref = null;
        if (is_array($found) && isset($found['ref_uuid'])) {$ref = $found['ref_uuid'];}
        if (!$ref && is_object($found) && property_exists($found,'ref_uuid')) {$ref = $found->ref_uuid;}
        if ($ref) {
            return Attribute::getThisAttribute(uuid: $ref );
        }
        throw new \LogicException("Could not find attribute in $array_key");
    }

    const CURSOR_ALL_LENGTH = -1;
    protected static function rebuildElementList(array $data,string $key,$cursor = null,?int $length = null ): ElementList
    {
        /** @var Collection<Element> $elements */
        $elements = $data[$key]??null;
        if ($elements === null) {
            throw new \LogicException("[rebuildElementList] Key $key not set");
        }
        if (count($elements) === 0) {
            /** @type ElementList */
            return ElementData::collect([], DataCollection::class);

        }

        $refs = [];
        foreach ($elements as $el) {
            $refs[] = $el->ref_uuid;
        }
        $build = Element::buildElement(
            given_uuids: $refs,
            b_do_namespace_relation: true, b_do_namespace_type_relation: true, b_do_type_relation: true,b_do_link_relation: true
        )->orderBy('id');

        if ($length === static::CURSOR_ALL_LENGTH) {
            $out = $build->cursorPaginate(perPage: count($refs));
        }
        elseif ($length === null ) {
            $out = $build->cursorPaginate(perPage: config('hbc.pagination.default_element_limit'), cursor: $cursor);
        }
        else {
            $out = $build->cursorPaginate(perPage: $length, cursor: $cursor);
        }

        /** @type ElementList */
        return ElementData::collect($out, CursorPaginatedDataCollection::class);
    }
}
