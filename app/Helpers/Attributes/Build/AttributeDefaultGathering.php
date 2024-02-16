<?php

namespace App\Helpers\Attributes\Build;

use App\Exceptions\HexbatchNotPossibleException;
use App\Exceptions\RefCodes;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\AttributeValuePointer;
use App\Models\Enums\Attributes\AttributeValueType;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AttributeDefaultGathering
{

    public ?AttributeValue $value;

    public ?AttributeValuePointer $pointer;

    /**
     * @throws \Exception
     */
    public function __construct(?Request $request , Attribute $attribute )
    {

        $this->pointer = null;
        $this->value = null;


        $value_block = new Collection();
        if ($request->request->has('value')) {
            $value_block = $request->collect('value');
        }
        $value_type = null;
        if (!$value_block->count()) {
            return;
        }
        if ($value_block->has('type')) {
            $convert = AttributeValueType::tryFrom($value_block->get('type'));
            $value_type = $convert ?: AttributeValueType::STRING;
        }
        if (!$value_type) { return;}

        $test_default = null;
        if ($value_block->has('default')) {
            $test_default = $value_block->get('default');
        }

        if (in_array($value_type, AttributeValueType::POINTER_TYPES)) {
            if (!is_string($test_default)) {
                throw new HexbatchNotPossibleException(__("msg.attribute_schema_pointers_string_only"),
                    \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY,
                    RefCodes::ATTRIBUTE_SCHEMA_ISSUE);
            }

            $this->pointer = AttributeValuePointer::createAttributeValue($attribute, $test_default, $value_type);

        } else {
            $this->value = AttributeValue::createValue($value_block);
        }



    }

    public function assign(Attribute $attribute) {
        if (!($this->value || $this->pointer) ) {
            return;
        }
        try {
            DB::beginTransaction();
            AttributeValuePointer::where('value_parent_attribute_id',$attribute->id)->delete();
            AttributeValue::where('parent_attribute_id',$attribute->id)->delete();

            $this->pointer?->save();
            $this->value?->save();
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






}
