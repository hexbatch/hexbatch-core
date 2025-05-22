<?php

namespace App\Sys\Res\Types;


use App\Enums\Attributes\TypeOfServerAccess;
use App\Enums\Types\TypeOfApproval;
use App\Exceptions\HexbatchInitException;
use App\Models\ElementType;
use App\Sys\Collections\SystemAttributes;
use App\Sys\Collections\SystemElements;
use App\Sys\Collections\SystemNamespaces;
use App\Sys\Collections\SystemServers;
use App\Sys\Collections\SystemTypes;
use App\Sys\Res\Atr\ISystemAttribute;
use App\Sys\Res\DocumentTrait;
use App\Sys\Res\Ele\ISystemElement;
use App\Sys\Res\IDocument;
use App\Sys\Res\ISystemResource;
use App\Sys\Res\Namespaces\ISystemNamespace;
use App\Sys\Res\Namespaces\Stock\ThisNamespace;
use App\Sys\Res\Servers\ISystemServer;
use App\Sys\Res\Servers\Stock\ThisServer;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds\DesignCreate;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ds\DesignParentAdd;
use App\Sys\Res\Types\Stk\Root\Act\Cmd\Ty\TypePublish;
use Hexbatch\Things\Interfaces\IThingAction;
use Illuminate\Support\Facades\DB;


abstract class BaseType implements ISystemType, IThingAction, IDocument
{
    use ActionableBaseTrait,DocumentTrait;

    protected ?ElementType $type = null;

    const UUID = '';
    const NAMESPACE_CLASS = ThisNamespace::class;

    const HANDLE_ELEMENT_CLASS = '';
    const SERVER_CLASS = ThisServer::class;

    const IS_FINAL = false;

    const TYPE_NAME = '';
    const ATTRIBUTE_CLASSES = [];

    const PARENT_CLASSES = [];

    protected bool $b_did_create_model = false;
    public function didCreateModel(): bool { return $this->b_did_create_model; }


    public function getISystemType() : ?ISystemType {return $this;}
    public static function getClassUuid() : string {
        return static::UUID;
    }


    public function getTypeServer() : ?ISystemServer {
        return SystemServers::getServerByUuid($this->getISystemType()::getTypeServerClass());
    }

    public function getType() : ElementType {
        if ($this->type) {return $this->type;}
        $maybe = ElementType::whereRaw('ref_uuid = ?',static::getClassUuid())->first();
        if ($maybe) {
            $this->type = $maybe;
        } else {
            $this->type = $this->makeType();
        }
        return $this->type;
    }
    public function makeType() :ElementType
   {
        if ($this->type) {return $this->type;}
        try
        {
            $design = new DesignCreate(type_name: static::getClassName(), is_final: $this->getISystemType()::isFinal(),
                access: TypeOfServerAccess::IS_PUBLIC, uuid: static::getClassUuid(),
                is_system: true,send_event: false
            );
            $design->runAction();
            $created_type = $design->getCreatedType();



            $this->b_did_create_model = true;
            return $created_type;

       } catch (\Exception $e) {
            throw new HexbatchInitException(message:$e->getMessage() .': code '.$e->getCode(),prev: $e);
       }
   }

    /**
     * @return ISystemAttribute[]
     */
    public static function getAttributeClasses() :array {
        return static::ATTRIBUTE_CLASSES;
    }

    public static function getSystemHandleElementClass() :string|ISystemElement {
        return static::HANDLE_ELEMENT_CLASS;
    }

    /** @return ISystemAttribute[]
     * @noinspection PhpUnused
     */
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
        $ret[static::getClassName()] = [] ;
        foreach (static::PARENT_CLASSES as $full_class_name) {
            $interfaces = class_implements($full_class_name);
            if (isset($interfaces['App\Sys\Res\Types\ISystemType'])) {
                /**
                 * @type ISystemType $full_class_name
                 */
                $ret[static::getClassName()][] = $full_class_name::getParentNameTree();
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



    public function getHandleElement(): ?ISystemElement
    {
        return SystemElements::getElementByUuid(static::HANDLE_ELEMENT_CLASS);
    }



    public function getTypeObject() : ?ElementType {
        return $this->getType();
    }

    public function getTypeNamespace() :?ISystemNamespace {
        return SystemNamespaces::getNamespaceByUuid($this->getISystemType()::getTypeNamespaceClass());
    }

    public static function getDictionaryObject() :ISystemType {
        return SystemTypes::getTypeByUuid(static::class);
    }

    public static function getClassName() :string { return static::TYPE_NAME; }
    public static function getTypeNamespaceClass() :string|ISystemNamespace { return static::NAMESPACE_CLASS; }
    public static function getTypeServerClass() :string|ISystemServer { return static::SERVER_CLASS; }
    public function isFinal(): bool { return static::IS_FINAL; }




    public function onCall(): ISystemResource
    {
        $this->getTypeObject();
        return $this;
    }

    public function onNextStep(): void
    {
        if (!$this->b_did_create_model) {return;}
        try
        {
            DB::beginTransaction();
            if (static::HANDLE_ELEMENT_CLASS) {
                $this->getTypeObject()->type_handle_element_id = $this->getISystemType()::getHandleElement()->getElementObject()->id;
                $this->getTypeObject()->save();
            }


            $parent_uuids = [];
            foreach ($this->getISystemType()::getParentTypes() as $parent_system_object) {
                $parent_uuids[] = $parent_system_object->getTypeObject()->getUuid();
            }
            $parenter = new DesignParentAdd(given_type_uuid: $this->getTypeObject()->getUuid(),
                given_parent_uuids: $parent_uuids, approval: TypeOfApproval::PUBLISHING_APPROVED, is_system: true, send_event: false);
            $parenter->runAction();


            $publish = new TypePublish(given_type_uuid: $this->getTypeObject()->getUuid(),is_system: true,send_event: false);
            $publish->runAction();
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw new HexbatchInitException(message:$e->getMessage() .': code '.$e->getCode(),prev: $e);
        }
    }




}
