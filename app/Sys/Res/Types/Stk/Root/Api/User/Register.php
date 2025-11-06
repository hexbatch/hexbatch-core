<?php

namespace App\Sys\Res\Types\Stk\Root\Api\User;


use App\Annotations\ApiParamMarker;
use App\Annotations\Documentation\HexbatchBlurb;
use App\Annotations\Documentation\HexbatchDescription;
use App\Annotations\Documentation\HexbatchTitle;
use App\Data\ApiParams\Data\User\Params\RegistrationParamData;
use App\Data\ApiParams\Data\User\Response\MeResponseData;
use App\Sys\Res\Types\Stk\Root\Act;
use App\Sys\Res\Types\Stk\Root\Api;
use Hexbatch\Thangs\Callables\CallableReturnStub;
use Hexbatch\Thangs\Data\Params\CommandParams;
use Hexbatch\Thangs\Enums\TypeOfCmdStatus;
use Hexbatch\Thangs\Helpers\ThangBuilder;
use Hexbatch\Thangs\Interfaces\ICmdCallReturn;
use Hexbatch\Thangs\Interfaces\ICommandCallable;
use Hexbatch\Thangs\Interfaces\IThangBuilder;
use Hexbatch\Thangs\Models\Thang;
use Illuminate\Support\Facades\Log;

#[HexbatchTitle( title: "Register")]
#[HexbatchBlurb( blurb: "Creates a new user and his default namespace")]
#[HexbatchDescription( description: "

  Makes a new user, his default namespace including a new type, which is used to build the home set, and public and private elements")]

#[ApiParamMarker( param_class: RegistrationParamData::class)]
class Register extends Api\UserApi implements  ICommandCallable
{
    const UUID = '6608f89f-ec12-427e-a653-9edc8acc5d19';
    const TYPE_NAME = 'api_user_register';


    const PARENT_CLASSES = [
        Api\UserApi::class,
        Act\Cmd\Us\UserRegister::class,
    ];


    public static function doCall(array $children_args, array $command_args): ICmdCallReturn
    {
        Log::debug("Called api register node");
        $b_approved = static::getDecisionUsingAndLogic($children_args);
        return new CallableReturnStub(status: $b_approved?TypeOfCmdStatus::CMD_SUCCESS:TypeOfCmdStatus::CMD_FAIL,
            data: ['children_args'=>$children_args,'user'=>$children_args['user']]);
    }


    /**
     * @throws \Throwable
     */
    public static function doRegistration(RegistrationParamData $params, array $tags = [], ?IThangBuilder $builder = null)
    :  MeResponseData|Thang
    {
        $my_command =  CommandParams::validateAndCreate([
            'command_class' =>static::class,
            'command_tags' =>array_merge(['register'],$tags)
        ]);
        ($builder?: $builder = ThangBuilder::createBuilder())
            ->tree($my_command);

        Act\Cmd\Us\UserRegister::doRegistration(
            params: $params,
            builder: $builder);


        $thang = $builder->execute()->getThang();
        if ($thang->getRootStatus() === TypeOfCmdStatus::CMD_SUCCESS) {
            return MeResponseData::from($thang->finished_data);
        } else {
            return $thang;
        }

    }

}

