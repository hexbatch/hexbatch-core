<?php
namespace App\Api\Cmd\Namespace\EditPromotion;


use App\Api\Cmd\IActionOaResponse;
use App\Api\Cmd\IActionWorker;
use App\Api\Cmd\IActionWorkReturn;
use App\Exceptions\HexbatchInvalidException;
use App\Exceptions\HexbatchNotFound;
use App\Exceptions\RefCodes;
use App\Models\Thing;
use App\Models\UserNamespace;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ns\NamespaceEditPromotion;



class NamespaceEditPromotionResponse extends NamespaceEditPromotion implements IActionWorkReturn,IActionOaResponse,IActionWorker
{


    public function __construct(
        protected ?UserNamespace $edited_namespace = null
    )
    {
    }

    public function toThing(Thing $thing)
    {

    }

    /**
     * @throws \Exception
     */
    protected function run(NamespaceEditPromotionParams $params) {


        $uuid = $params->getUuid();
        $ns = $this->edited_namespace = UserNamespace::whereRaw("? = user_namespaces.ref_uuid",[$uuid])->first();
        if (!$ns) {
            throw new HexbatchNotFound(__("msg.namespace_not_found", ['ref' => $uuid]),
                \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND,
                RefCodes::NAMESPACE_NOT_FOUND);
        }
        if ($params->getNamespaceUserId()) {
            $ns->namespace_user_id = $params->getNamespaceUserId();
        }

        if ($params->getNamespaceServerId()) {
            $ns->namespace_server_id = $params->getNamespaceServerId();
        }

        if ($params->getNamespaceTypeId()) {
            $ns->namespace_type_id = $params->getNamespaceTypeId();
        }

        if ($params->getPublicElementId()) {
            $ns->public_element_id = $params->getPublicElementId();
        }

        if ($params->getPrivateElementId()) {
            $ns->private_element_id = $params->getPrivateElementId();
        }

        if ($params->getNamespaceHomeSetId()) {
            $ns->namespace_home_set_id = $params->getNamespaceHomeSetId();
        }

        if ($params->getNamespacePublicKey()) {
            $ns->namespace_public_key = $params->getNamespacePublicKey();
        }

        if ($params->getNamespaceName()) {
            $ns->namespace_name = $params->getNamespaceName();
        }

        $ns->save();

    }

    /**
     * @throws \Exception
     */
    public static function doWork($params): IActionWorkReturn
    {
        if (!(is_a($params,NamespaceEditPromotionParams::class) || is_subclass_of($params,NamespaceEditPromotionParams::class))) {
            throw new HexbatchInvalidException("Params is not NamespaceEditPromotionParams");
        }
        $worker = new NamespaceEditPromotionResponse();
        $worker->run($params);
        return $worker;
    }

    public function getEditedNamespace(): ?UserNamespace
    {
        return $this->edited_namespace;
    }





}
