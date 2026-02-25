<?php

namespace App\Sys\Res\Types\Stk\Root\Api\Design;


use App\Data\ApiParams\Data\Locations\Location;
use App\Helpers\Utilities;
use App\Models\LocationBound;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use BlueM\Tree;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Data\Params\CommandParams;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Facades\Log;

class EditLocation extends CreateLocation
{
    const UUID = '092ffcc2-8feb-4922-9325-fcb3d197886d';
    const TYPE_NAME = 'api_design_edit_location';


    const PARENT_CLASSES = [
        Api\DesignApi::class,
        Act\Cmd\Ds\DesignLocationEdit::class,
    ];


    public function getChildrenTree(): ?Tree
    {


        $nodes = [];
        $creator = new Act\Cmd\Ds\DesignLocationEdit(
            bound_name: $this->params->bound_name,
            given_location_uuid: $this->params->ref_uuid,
            location_type: $this->params->location_type,
            geo_json: $this->params->geo_json,
            display: $this->params->location_display,
            parent_action_data: $this->action_data,tags: ['edit location bound from api']);
        $nodes[] = ['id' => $creator->getActionData()->id, 'parent' => -1, 'title' => $creator->getType()->getName(),'action'=>$creator];


        //last in tree is the
        if (count($nodes)) {
            return new Tree(
                $nodes,
                ['rootId' => -1]
            );
        }
        return null;

    }


    /**
     * @throws \Exception
     */
    public function setChildActionResult(IThingAction $child): void {

        if ($child instanceof Act\Cmd\Ds\DesignLocationEdit) {
            if ($child->isActionFail() || $child->isActionError()) {
                $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            }
            else {
                if ($child->isActionSuccess() && $child->getGivenType()) {
                    $this->setGivenLocationBound($child->getGivenLocationBound());
                    $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
                } else {
                    $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
                }
            }
        }
    }


    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api edit time node");
        return new CallableReturnStub(status: TypeOfCmdStatus::CMD_SUCCESS,data: $children_args);
    }

    /** @throws \Throwable */
    public static function editLocation(UserNamespace $namespace,LocationBound $bound,?Location $params = null, array $tags = [], ?IThangBuilder $builder = null)
    : LocationBound|Thang
    {
        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge(['edit-location'],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->setNamespace($namespace)
            ->setSharedArg('namespace',$namespace)
            ->setSharedArg('given_bound',$bound)
            ->tree($my_command)
            ->leaf([
                'command_class' =>Act\Cmd\Ds\DesignLocationEdit::class,
                'command_args' =>[
                    'location_params'=>$params?->toArray(),
                    'namespace_uuid'=>Utilities::getCurrentNamespace()->ref_uuid
                ],
                'command_tags' =>[Act\Cmd\Ds\DesignLocationEdit::class]
            ]);

        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            $data = $thang->finished_data;
            /** @var LocationBound|null $location_bound */
            $location_bound = LocationBound::buildLocationBound(uuid: $data['ref_uuid'])->first();
            return $location_bound;
        } else {
            return $thang;
        }

    }

}

