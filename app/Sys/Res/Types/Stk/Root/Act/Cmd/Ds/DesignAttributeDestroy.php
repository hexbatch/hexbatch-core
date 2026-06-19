<?php

namespace App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds;

use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Enums\Sys\TypeOfAction;
use App\Models\Attribute;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Illuminate\Support\Facades\Log;


#[HexbatchTitle( title: "Destroys an attribute")]
#[HexbatchBlurb( blurb: "Attributes can be destroyed while in design phase. No events are raised")]
#[HexbatchDescription( description:'
# Destroy an attribute in design mode

The only time an attribute can be destroyed is before publishing a type.
The attribute and type parents are not notified when this is destroyed. They will have a chance to see this when the publishing is made.


')]

class DesignAttributeDestroy extends Act\Cmd\Ds implements ICommandCallable
{
    const UUID = '079cfc62-0fa2-47f1-84c0-df0fa90441c5';
    const ACTION_NAME = TypeOfAction::CMD_DESIGN_ATTRIBUTE_DESTROY;

    const ATTRIBUTE_CLASSES = [
    ];

    const PARENT_CLASSES = [
        Act\Cmd\Ds::class
    ];







    public static function destroyAttribute(Attribute $given_attribute,UserNamespace $namespace) : Attribute
    {
        static::checkIfGivenIsAdmin(given: $namespace,target: $given_attribute->type_owner->owner_namespace);
        $given_attribute->delete();
        return $given_attribute;
    }



    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        $attribute = $command_args['given_attribute'];
        $namespace = $command_args['namespace'];
        $deleted_attribute = static::destroyAttribute( given_attribute: $attribute, namespace: $namespace);
        Log::debug("Called design attribute destroy node",['args'=>$command_args,'attribute'=>$deleted_attribute]);
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $deleted_attribute->toArray());
    }
}

