<?php

namespace App\Helpers\Attributes\Build;

use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Helpers\Utilities;
use App\Models\Attribute;
use App\Models\AttributeValuePointer;
use App\Models\Enums\AttributeValueType;
use ErrorException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AttributeValue
{

    public bool $is_nullable;
    public ?AttributeValueType $value_type;
    public ?int $value_numeric_min;
    public ?int $value_numeric_max;
    public ?string $value_regex;
    public ?array $value_default;

    public ?AttributeValuePointer $pointer;

    /**
     * @throws \Exception
     */
    public function __construct(?Request $request , Attribute $attribute )
    {
        $this->is_nullable = true;
        $this->value_type = AttributeValueType::STRING;
        $this->value_numeric_min = null;
        $this->value_numeric_max = null;
        $this->value_regex = null;
        $this->value_default = null;
        $this->pointer = null;


        $value_block = new Collection();
        if ($request->request->has('value')) {
            $value_block = $request->collect('value');
        }
        if (!$value_block->count()) {
            $this->value_type = null;
            return;
        }

        if ($value_block->has('is_nullable')) {
            $this->is_nullable = Utilities::boolishToBool($value_block->get('is_nullable'));
        }

        if ($value_block->has('type')) {
            $convert = AttributeValueType::tryFrom($value_block->get('type'));
            $this->value_type = $convert ?: AttributeValueType::STRING;
        }

        if (in_array($this->value_type, AttributeValueType::NUMERIC_TYPES)) {
            if ($value_block->has('min')) {
                $this->value_numeric_min = (float)$value_block->get('min');
            }
            if ($value_block->has('max')) {
                $this->value_numeric_max = (float)$value_block->get('max');
            }
        }

        if (in_array($this->value_type, AttributeValueType::STRING_TYPES)) {
            if ($value_block->has('regex')) {
                $bare_regex = trim($value_block->get('regex'), '/');
                $test_regex = "/$bare_regex/";
                $issues = Utilities::regexHasErrors($test_regex);
                if ($issues) {
                    throw new HexbatchNotPossibleException(__("msg.attribute_schema_bad_regex", ['issue' => $issues]),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                }
                $this->value_regex = $test_regex;
            }

        }

        if ($value_block->has('default')) {
            $test_default = $value_block->get('default');

            if (in_array($this->value_type, AttributeValueType::STRING_TYPES) || in_array($this->value_type, AttributeValueType::NUMERIC_TYPES)) {
                if (is_array($test_default) || is_object($test_default)) {
                    $b_ok = false;
                } else {
                    $b_ok = $this->validateScalarValue($test_default);
                }
                if (!$b_ok) {
                    throw new HexbatchNotPossibleException(__("msg.attribute_schema_bad_scalar_default"),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                }
                $this->value_default = ['value_default'=>$this->castScalarValue($test_default)];
            } elseif ($this->value_type === AttributeValueType::JSON) {
                if (is_string($test_default)) {
                    $json_issue = Utilities::jsonHasErrors($test_default);
                    if ($json_issue) {
                        throw new HexbatchNotPossibleException(__("msg.this_is_bad_json", ['issue' => $json_issue]),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                    }
                    $this->value_default = json_decode($test_default, true);
                } else {
                    $this->value_default = $test_default;
                }
            } elseif (in_array($this->value_type, AttributeValueType::COORDINATION_TYPES)) {
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
                switch ($this->value_type) {
                    case AttributeValueType::COORDINATE_MAP:
                    {
                        if (
                            !array_key_exists('latitude',$maybe_coordination )
                            || !array_key_exists('longitude',$maybe_coordination)
                            || ($maybe_coordination['longitude'] > 180 || $maybe_coordination['longitude'] < -180)
                            || ($maybe_coordination['latitude'] > 90 || $maybe_coordination['latitude'] < -900)
                        ) {
                            throw new HexbatchNotPossibleException(__("msg.attribute_schema_improper_map_coordinate"),
                                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                                RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                        }
                        $this->value_default = $maybe_coordination;
                        break;
                    }
                    case AttributeValueType::COORDINATE_SHAPE:
                    {
                        if (
                            !array_key_exists('x',$maybe_coordination )
                            || !array_key_exists('y',$maybe_coordination )
                            || !array_key_exists('z',$maybe_coordination )
                        ) {
                            throw new HexbatchNotPossibleException(__("msg.attribute_schema_improper_shape_coordinate"),
                                \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                                RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                        }
                        $this->value_default = $maybe_coordination;
                        break;
                    }
                    default:
                    {
                        throw new HexbatchNotPossibleException(__("msg.attribute_schema_unsupported_value", ['type' => $this->value_type->value]),
                            \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                            RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                    }
                }

            } elseif (in_array($this->value_type, AttributeValueType::POINTER_TYPES)) {
                if (!is_string($test_default)) {
                    throw new HexbatchNotPossibleException(__("msg.attribute_schema_pointers_string_only"),
                        \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                        RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
                }
                $this->value_default = null;
                $this->pointer = AttributeValuePointer::createAttributeValue($attribute, $test_default, $this->value_type);
            }

        }



    }

    public function assign(Attribute $attribute) {
        if (!$this->value_type) {
            return;
        }
        try {
            DB::beginTransaction();
            AttributeValuePointer::where('value_parent_attribute_id',$attribute->id)->delete();
            foreach ($this as $key => $val) {
                if ($key === 'pointer') { continue;}
                $attribute->$key = $val;
            }
            $this->pointer?->save();
            $attribute->save();
            DB::commit();
        } catch (HexbatchNotPossibleException|\LogicException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e){
            DB::rollBack();
            /** @noinspection PhpUnhandledExceptionInspection */
            throw $e;
        }
    }

    public function validateScalarValue(bool|int|float|string|null $what) : bool {

        if (is_null($what) ) {
            if ($this->is_nullable) {
                return true;
            }
            return false;
        }

        if(in_array($this->value_type,AttributeValueType::STRING_TYPES ) ) {
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

        if (in_array($this->value_type,AttributeValueType::NUMERIC_TYPES )) {
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
    public function castScalarValue(bool|int|float|string|null $what) : int|float|string|null {

        if (is_null($what) && $this->is_nullable) {
            return null;
        }
        if(in_array($this->value_type,AttributeValueType::STRING_TYPES )) {
            return (string)$what;
        }

        if (in_array($this->value_type,AttributeValueType::NUMERIC_TYPES )) {
            return (float)$what;
        }
        throw new \LogicException("cannot cast the scaler value because the type is not scaler");
    }




}
