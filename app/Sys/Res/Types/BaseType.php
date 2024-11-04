<?php

namespace App\Sys\Res\Types;


use App\Api\Cmd\Design\Promote\DesignPromoteParams;
use App\Api\Cmd\Design\Promote\DesignPromoteResponse;
use App\Api\Cmd\Design\Promote\SetupForSystem;
use App\Exceptions\HexbatchInitException;
use App\Models\ElementType;
use App\Sys\Build\ActionMapper;
use App\Sys\Build\BuildActionFacet;
use App\Sys\Collections\SystemAttributes;
use App\Sys\Collections\SystemElements;
use App\Sys\Collections\SystemNamespaces;
use App\Sys\Collections\SystemServers;
use App\Sys\Collections\SystemTypes;
use App\Sys\Res\Atr\ISystemAttribute;
use App\Sys\Res\Ele\ISystemElement;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Namespaces\ISystemNamespace;
use App\Sys\Res\Namespaces\Stock\ThisServerNamespace;
use App\Sys\Res\Servers\ISystemServer;
use App\Sys\Res\Servers\Stock\ThisServer;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds\DesignPromotion;

abstract class BaseType implements ISystemType
{
    protected ?ElementType $type;

    const UUID = '';
    const NAMESPACE_CLASS = ThisServerNamespace::class;

    const HANDLE_ELEMENT_CLASS = '';
    const SERVER_CLASS = ThisServer::class;

    const IS_FINAL = false;

    const TYPE_NAME = '';
    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [];

    public static function getClassUuid() : string {
        return static::UUID;
    }


    public function getClassServer() : ?ISystemServer {
        return SystemServers::getServerByUuid(static::SERVER_CLASS);
    }

    public function makeType() :?ElementType
   {

        try
        {
            $sys_params = new SetupForSystem;
            $sys_params
               ->setUuid(static::getClassUuid())
               ->setTypeName(static::getClassTypeName())
               ->setSystem(true)
               ->setFinalType(static::IS_FINAL);


            /**
            * @var DesignPromoteParams $promo_params
            */
            $promo_params = ActionMapper::getActionInterface(BuildActionFacet::FACET_PARAMS,DesignPromotion::getClassUuid());
            $promo_params->fromCollection($sys_params->makeCollection());

            /**
            * @type DesignPromoteResponse $promo_work
            */
            $promo_work = ActionMapper::getActionInterface(BuildActionFacet::FACET_WORKER,DesignPromotion::getClassUuid());

            $promo_results = $promo_work::doWork($promo_params);
            return $promo_results->getGeneratedType();

       } catch (\Exception $e) {
            throw new HexbatchInitException($e->getMessage(),$e->getCode(),null,$e);
       }
   }

    public static function getAttributeClasses() :array {
        return static::ATTRIBUTE_CLASSES;
    }

    public static function getSystemHandleElementClass() :string|ISystemElement {
        return static::HANDLE_ELEMENT_CLASS;
    }

    /** @return ISystemAttribute[] */
    public function getAttributes() :array {
        $ret = [];
        foreach (static::ATTRIBUTE_CLASSES as $class_name) {
            $ret[] = SystemAttributes::getAttributeByUuid($class_name);
        }
        return $ret;
    }

    /** @return ISystemType[] */
    public function getParentTypes() :array {
        $ret = [];
        foreach (static::PARENT_CLASSES as $class_name) {
            $ret[] = SystemTypes::getTypeByUuid($class_name);
        }
        return $ret;
    }


    public static function hasInAncestors(string $target_full_class_name) :bool  {
        if (static::class === $target_full_class_name) {return true;}
        foreach (static::PARENT_CLASSES as $full_class_name) {
            if ($full_class_name === $target_full_class_name) {return true;}
            $interfaces = class_implements($full_class_name);
            if (isset($interfaces['App\Sys\Res\Types\ISystemType'])) {
                /**
                 * @type ISystemType $full_class_name
                 */
                $ret = $full_class_name::hasInAncestors($target_full_class_name);
                if ($ret) {return true;}
            }
        }
        return false;
    }

    public static function getParentNameTree() :array  {
        $ret = [];
        $ret[static::getClassTypeName()] = [] ;
        foreach (static::PARENT_CLASSES as $full_class_name) {
            $interfaces = class_implements($full_class_name);
            if (isset($interfaces['App\Sys\Res\Types\ISystemType'])) {
                /**
                 * @type ISystemType $full_class_name
                 */
                $ret[static::getClassTypeName()][] = $full_class_name::getParentNameTree();
            }
        }
        return $ret;
    }

    public static function getFlatInheritance() : string  {
        $raw = static::renderSubtree(static::getParentNameTree());
        return preg_replace('/(\|~\|\d)/', "\n   ",$raw);
    }

    public static function renderSubtree(array $tree) : string  {
        $ret = [];

        $count = 0;
        foreach ($tree as $k => $v) {
            if ($count) {
                $ret[] = '~';
            }
            if ($k) {
                $ret[] = $k;
            }

            if (count($v) ) {
                $what = static::renderSubtree($v);
                $ret[] = $what;

            }
            $count++;

        }


        return implode('|',$ret);
    }



    public function getDescriptionElement(): ?ISystemElement
    {
        return SystemElements::getElementByUuid(static::HANDLE_ELEMENT_CLASS);
    }



    public function getTypeObject() : ?ElementType {
        if ($this->type) {return $this->type;}
        $this->type = $this->makeType();
        return $this->type;
    }

    public function getTypeNamespace() :?ISystemNamespace {
        return SystemNamespaces::getNamespaceByUuid(static::NAMESPACE_CLASS);
    }

    public static function getClassTypeName() :string { return static::TYPE_NAME; }
    public function isFinal(): bool { return false; }




    public function onCall(): ISystemResource
    {
        $this->getTypeObject();
        return $this;
    }

    public function onNextStep(): void
    {
        //users add in the default namespace using the uuid of the now generated ns
        $ns = $this->getTypeNamespace();
        if (!$ns) {
            throw new HexbatchInitException('type next step cannot get ns');
        }

        $this->getTypeObject()->owner_namespace_id = $ns->getNamespaceObject()->id;

        $this->getTypeObject()->save();

        //todo set the handle here
    }



}
