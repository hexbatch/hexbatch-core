<?php

namespace App\Models;

use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Attributes\Apply\StandardAttributes;
use App\Helpers\Utilities;
use App\Models\Enums\Attributes\AttributeConstantPolicy;
use App\Models\Enums\Attributes\AttributeRemoteUsePolicy;
use App\Models\Enums\Attributes\AttributeValueType;
use ArrayObject;
use ErrorException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;


/**
 * @mixin Builder
 * @mixin \Illuminate\Database\Query\Builder
 * @property int id
 * @property int parent_attribute_id
 * @property boolean is_nullable
 * @property AttributeValueType value_type
 * @property AttributeRemoteUsePolicy remote_use_policy
 * @property AttributeConstantPolicy constant_policy
 * @property int value_numeric_min
 * @property int value_numeric_max
 * @property string value_regex
 * @property ArrayObject json_value_default
 * @property string text_value_default
 *
 *
 * @property int created_at_ts
 * @property int updated_at_ts
 * @property Attribute value_parent
 *
 */
class AttributeValue extends Model
{

    protected $table = 'attribute_values';
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
        'json_value_default' => AsArrayObject::class,
        'value_type' => AttributeValueType::class,
        'remote_use_policy' => AttributeRemoteUsePolicy::class,
    ];

    public function value_parent() : BelongsTo {
        return $this->belongsTo('App\Models\Attribute','parent_attribute_id');
    }


    public function getValue() {
        if ($this->value_type === AttributeValueType::STRING) {
            return $this->text_value_default;
        }else if ($this->value_type === AttributeValueType::NUMERIC) {
            return $this->json_value_default['value_default']??null;
        } else if ($this->value_type === AttributeValueType::JSON) {
            return $this->json_value_default;
        } else {
            /** @uses AttributeValue::value_parent() */
            return $this->value_parent?->attribute_pointer?->getValue();
        }

    }

    public static function createValue(Collection $value_block,?Attribute $parent = null) : AttributeValue {

        $ret = new AttributeValue();
        if ($parent) {
            $ret->parent_attribute_id = $parent->id;
        }
        if ($value_block->has('is_nullable')) {
            $ret->is_nullable = Utilities::boolishToBool($value_block->get('is_nullable'));
        }

        if ($value_block->has('type')) {
            $convert = AttributeValueType::tryFrom($value_block->get('type'));
            $ret->value_type = $convert ?: AttributeValueType::STRING;
        }

        if ($ret->value_type === AttributeValueType::NUMERIC) {
            if ($value_block->has('min')) {
                $ret->value_numeric_min = (float)$value_block->get('min');
            }
            if ($value_block->has('max')) {
                $ret->value_numeric_max = (float)$value_block->get('max');
            }
        }

        if ($ret->value_type === AttributeValueType::STRING) {
            if ($value_block->has('regex')) {
                $bare_regex = trim($value_block->get('regex'), '/');
                $test_regex = "/$bare_regex/";
                $issues = Utilities::regexHasErrors($test_regex);
                if ($issues) {
                    throw new HexbatchNotPossibleException(__("msg.attribute_schema_bad_regex", ['issue' => $issues]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                }
                $ret->value_regex = $test_regex;
            }

        }

        if ($value_block->has('default')) {
            $test_default = $value_block->get('default');

            if (in_array($ret->value_type, AttributeValueType::SCALER_TYPES)) {
                if (is_array($test_default) || is_object($test_default)) {
                    $b_ok = false;
                } else {
                    $b_ok = $ret->validateScalarValue($test_default);
                }
                if (!$b_ok) {
                    throw new HexbatchNotPossibleException(__("msg.attribute_schema_bad_scalar_default"),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                }
                $ret->text_value_default = $ret->castScalarValue($test_default);
            } elseif ($ret->value_type === AttributeValueType::JSON) {
                if (is_string($test_default)) {
                    $json_issue = Utilities::jsonHasErrors($test_default);
                    if ($json_issue) {
                        throw new HexbatchNotPossibleException(__("msg.this_is_bad_json", ['issue' => $json_issue]),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                    }
                    $ret->json_value_default = json_decode($test_default, true);
                } else {
                    $ret->json_value_default = $test_default;
                }
            } elseif (in_array($ret->value_type, AttributeValueType::COORDINATION_TYPES)) {
                if (is_string($test_default)) {
                    $json_issue = Utilities::jsonHasErrors($test_default);
                    if ($json_issue) {
                        throw new HexbatchNotPossibleException(__("msg.this_is_bad_json", ['issue' => $json_issue]),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                    }
                    $maybe_coordination = json_decode($test_default, true);
                    if (!is_object($maybe_coordination)) {
                        throw new HexbatchNotPossibleException(__("msg.attribute_schema_json_no_primitive"),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                    }
                } else {
                    $maybe_coordination = $test_default;
                }
                switch ($ret->value_type) {
                    case AttributeValueType::COORDINATE_MAP:
                    {
                        if (!StandardAttributes::validateMapLocation($maybe_coordination, false)) {
                            throw new HexbatchNotPossibleException(__("msg.attribute_schema_improper_map_coordinate"),
                                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                                RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                        }
                        $ret->json_value_default = $maybe_coordination;
                        break;
                    }
                    case AttributeValueType::COORDINATE_SHAPE:
                    {
                        if (!StandardAttributes::validateShapeLocation($maybe_coordination, false)) {
                            throw new HexbatchNotPossibleException(__("msg.attribute_schema_improper_shape_coordinate"),
                                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                                RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                        }
                        $ret->json_value_default = $maybe_coordination;
                        break;
                    }
                    default:
                    {
                        throw new HexbatchNotPossibleException(__("msg.attribute_schema_unsupported_value", ['type' => $ret->value_type->value]),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                    }
                }

            }
        }
        return $ret;
    }

    public function validateScalarValue(bool|int|float|string|null $what) : bool {

        if (is_null($what) ) {
            if ($this->is_nullable) {
                return true;
            }
            return false;
        }

        if($this->value_type === AttributeValueType::STRING )  {
            if (!$this->value_regex) {
                return true;
            }
            try {
                if (preg_match($this->value_regex, $what) ) {
                    return true;
                }
                return false;
            } /** @noinspection PhpRedundantCatchClauseInspection */ catch (ErrorException) {
                return false;
            }
        }

        if ($this->value_type === AttributeValueType::NUMERIC ) {
            if ( !is_null($this->value_numeric_max )) {
                if ($what > $this->value_numeric_max) {
                    return false;
                }
            }

            if ( !is_null($this->value_numeric_min )) {
                if ($what < $this->value_numeric_min) {
                    return false;
                }
            }

            return true;
        }
        return false;
    }
    public function castScalarValue(bool|int|float|string|null $what) : float|string|null {

        if (is_null($what) && $this->is_nullable) {
            return null;
        }
        if($this->value_type === AttributeValueType::STRING ) {
            return (string)$what;
        }

        if($this->value_type === AttributeValueType::NUMERIC ) {
            return (float)$what;
        }
        throw new \LogicException("cannot cast the scaler value because the type is not scaler");
    }


}
