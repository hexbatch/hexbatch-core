<?php
namespace App\Api\Cmd\Namespace\Promote;


use App\Api\Cmd\IActionOaResponse;
use App\Api\Cmd\IActionWorker;
use App\Api\Cmd\IActionWorkReturn;
use App\Exceptions\HexbatchInvalidException;
use App\Models\Thing;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns\NamespacePromote;


class NamespacePromoteResponse extends NamespacePromote implements IActionWorkReturn,IActionOaResponse,IActionWorker
{


    public function __construct(
        protected ?UserNamespace $generated_namespace = null
    )
    {
    }

    public function toThing(Thing $thing)
    {

    }

    /**
     * @throws \Exception
     */
    protected function run(NamespacePromoteParams $params) {


        $ns = new UserNamespace();
        $ns->ref_uuid = $params->getUuid();
        $ns->namespace_user_id = $params->getNamespaceUserId();
        $ns->namespace_server_id = $params->getNamespaceServerId();
        $ns->namespace_type_id = $params->getNamespaceTypeId();
        $ns->public_element_id = $params->getPublicElementId();
        $ns->private_element_id = $params->getPrivateElementId();
        $ns->namespace_home_set_id = $params->getNamespaceHomeSetId();
        $ns->namespace_public_key = $params->getNamespacePublicKey();
        $ns->namespace_name = $params->getNamespaceName();
        $ns->is_system = $params->isSystem();

        $ns->save();
        $this->generated_namespace = $ns;
    }

    /**
     * @throws \Exception
     */
    public static function doWork($params): IActionWorkReturn
    {
        if (!(is_a($params,NamespacePromoteParams::class) || is_subclass_of($params,NamespacePromoteParams::class))) {
            throw new HexbatchInvalidException("Params is not NamespacePromoteParams");
        }
        $worker = new NamespacePromoteResponse();
        $worker->run($params);
        return $worker;
    }

    public function getGeneratedNamespace(): ?UserNamespace
    {
        return $this->generated_namespace;
    }





}
