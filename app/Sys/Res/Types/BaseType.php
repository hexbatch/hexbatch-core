<?php

namespace App\Sys\Res\Types;


use App\Enums\Attributes\TypeOfServerAccess;
use App\Enums\Sys\TypeOfFlag;
use App\Enums\Types\TypeOfApproval;
use App\Exceptions\HexbatchInitException;
use App\Helpers\Utilities;
use App\Models\ActionDatum;
use App\Models\Attribute;
use App\Models\Element;
use App\Models\ElementSet;
use App\Models\ElementType;
use App\Models\Server;
use App\Models\UserNamespace;
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

    public function getType(bool $b_construct_if_missing = true) : ?ElementType {
        if ($this->type) {return $this->type;}
        $maybe = ElementType::whereRaw('ref_uuid = ?',static::getClassUuid())->first();
        if ($maybe) {
            $this->type = $maybe;
        } else {
            if ($b_construct_if_missing) {
                $this->type = $this->makeType();
            } else {
                return null;
            }

        }
        return $this->type;
    }
    public function makeType() :ElementType
   {
        if ($this->type) {return $this->type;}
        try
        {
            $design = new DesignCreate(type_name: static::getHexbatchClassName(),
                owner_namespace_uuid: $this->getISystemType()->getTypeNamespace()::getClassUuid(),
                is_final: $this->getISystemType()::isFinal(),
                access: TypeOfServerAccess::IS_PUBLIC,
                uuid: static::getClassUuid(), is_system: true, send_event: false
            );
            $design->runAction();
            $created_type = $design->getDesignType();



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
        $ret[static::getHexbatchClassName()] = [] ;
        foreach (static::PARENT_CLASSES as $full_class_name) {
            $interfaces = class_implements($full_class_name);
            if (isset($interfaces['App\Sys\Res\Types\ISystemType'])) {
                /**
                 * @type ISystemType $full_class_name
                 */
                $ret[static::getHexbatchClassName()][] = $full_class_name::getParentNameTree();
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

    public static function getFullClassName() :string {return static::class;}
    public static function getHexbatchClassName() :string { return static::TYPE_NAME; }
    public static function getTypeNamespaceClass() :string|ISystemNamespace { return static::NAMESPACE_CLASS; }
    public static function getTypeServerClass() :string|ISystemServer { return static::SERVER_CLASS; }
    public function isFinal(): bool { return static::IS_FINAL; }



    public function onCall(): ISystemResource
    {
        $this->getTypeObject();
        return $this;
    }

    public function onNextStepC(): void {
        if (!$this->b_did_create_model) {return;}
        try
        {
            DB::beginTransaction();
            if (static::HANDLE_ELEMENT_CLASS) {
                $this->getTypeObject()->type_handle_element_id = $this->getISystemType()::getHandleElement()->getElementObject()->id;
                $this->getTypeObject()->imported_from_server_id = $this->getTypeServer()->getServerObject()?->id;
                $this->getTypeObject()->save();
            }
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw new HexbatchInitException(message:$e->getMessage() .': code '.$e->getCode(),prev: $e);
        }
    }

    public function onNextStepB(): void {

        if (!$this->b_did_create_model) {return;}
        try
        {
            DB::beginTransaction();

            $publish = new TypePublish(given_type_uuid: $this->getTypeObject()->getUuid(),
                is_system: true,send_event: false,publishing_status: TypeOfApproval::PUBLISHING_APPROVED);
            $publish->runAction();
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw new HexbatchInitException(message:$e->getMessage() .': code '.$e->getCode(),prev: $e);
        }

    }
    public function onNextStep(): void
    {
        if (!$this->b_did_create_model) {return;}
        try
        {
            DB::beginTransaction();


            $parent_uuids = [];
            foreach ($this->getISystemType()::getParentTypes() as $parent_system_object) {
                $parent_uuids[] = $parent_system_object->getTypeObject()->getUuid();
            }
            if (count($parent_uuids)) {
                $parenter = new DesignParentAdd(given_type_uuid: $this->getTypeObject()->getUuid(), given_parent_uuids: $parent_uuids,
                    approval: TypeOfApproval::PUBLISHING_APPROVED, is_system: true, send_event: false,
                    );
                $parenter->runAction();
            }
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw new HexbatchInitException(message:$e->getMessage() .': code '.$e->getCode(),prev: $e);
        }
    }


    public function __construct(
        protected ?ActionDatum   $action_data = null,
        protected ?ActionDatum   $parent_action_data = null,
        protected ?UserNamespace $owner_namespace = null,
        protected bool           $b_type_init = false,
        protected bool           $is_system = false,
        protected bool           $send_event = true,
        protected ?bool          $is_async = null,
        protected array          $tags = [],

    )
    {

        if ($this->b_type_init) {
            return;
        }

        if ($this->action_data) {  $this->restoreData(); } else {$this->initData();}

    }

    const array ACTIVE_COLLECTION_KEYS = [];
    const array ACTIVE_DATA_KEYS = [];

    protected function runActionInner(array $data = []): void {
        Utilities::ignoreVar($data);
    }

    protected function postActionInner(array $data = []): void {
        Utilities::ignoreVar($data);
    }

    protected function getMyData() :array { return []; }

    public function getImportantValue(): mixed
    {
        if ($this->action_data) {
            if ($this->action_data->collection_data?->offsetExists('important_value')) {
                return $this->action_data->collection_data->offsetGet('important_value');
            } else {
                return null;
            }
        }
        throw new \LogicException("Important value not set up, or action data not existing");
    }

    public function setImportantValue(mixed $what, bool $b_save = false): static
    {
        $this->action_data?->collection_data->offsetSet('important_value', $what);
        if ($b_save) {$this->action_data?->save();}
        return $this;
    }


    public function hasFlag(TypeOfFlag $what): bool
    {
        if ($this->action_data) {
            if ($this->action_data->collection_data?->offsetExists('flag_'.$what->value)) {
                return (bool)$this->action_data->collection_data->offsetGet('flag_'.$what->value);
            } else {
                return false;
            }
        }
        throw new \LogicException("No action data");
    }

    public function setFlag(TypeOfFlag $what, bool $b_save = false): static
    {
        $this->action_data?->collection_data->offsetSet('flag_'.$what->value,true );
        if ($b_save) {$this->action_data?->save();}
        return $this;
    }


    public function getGivenType(): ?ElementType
    {   /** @uses ActionDatum::data_type() */
        return $this->action_data->data_type;
    }

    public function setGivenType(null|ElementType|string $what, bool $b_save = false): static
    {
        if ($what instanceof ElementType) {
            $this->action_data->data_type_id = $what->id;
        } else if ($what) {
            $this->action_data->data_type_id = ElementType::getElementType(uuid: $what)->id;
        }
        if ($b_save) {$this->action_data->save();}
        return $this;
    }

    public function getGivenSet(): ?ElementSet
    {   /** @uses ActionDatum::data_set() */
        return $this->action_data->data_set;
    }

    public function setGivenSet(null|ElementSet|string $what, bool $b_save = false): static
    {
        if ($what instanceof ElementSet) {
            $this->action_data->data_set_id = $what->id;
        } else if ($what) {
            $this->action_data->data_set_id = ElementSet::getThisSet(uuid: $what)->id;
        }
        if ($b_save) {$this->action_data->save();}
        return $this;
    }



    public function getGivenElement(): ?Element
    {   /** @uses ActionDatum::data_element() */
        return $this->action_data->data_element;
    }

    public function setGivenElement(null|Element|string $what, bool $b_save = false ): static
    {
        if ($what instanceof Element) {
            $this->action_data->data_element_id = $what->id;
        } else if ($what) {
            $this->action_data->data_element_id = Element::getThisElement(uuid: $what)->id;
        }
        if ($b_save) {$this->action_data->save();}
        return $this;
    }

    public function getGivenAttribute(): ?Attribute
    {   /** @uses ActionDatum::data_attribute() */
        return $this->action_data->data_attribute;
    }

    public function setGivenAttribute(null|Attribute|string $what, bool $b_save = false): static
    {
        if ($what instanceof Attribute) {
            $this->action_data->data_attribute_id = $what->id;
        } else if ($what) {
            $this->action_data->data_attribute_id = Attribute::getThisAttribute(uuid: $what)->id;
        }
        if ($b_save) {$this->action_data->save();}
        return $this;
    }

    public function getGivenServer(): ?Server
    {   /** @uses ActionDatum::data_server() */
        return $this->action_data->data_server;
    }

    public function setGivenServer(null|Server|string $what, bool $b_save = false): static
    {
        if ($what instanceof Server) {
            $this->action_data->data_server_id = $what->id;
        } else if ($what) {
            $this->action_data->data_server_id = Server::getThisServer(uuid: $what)->id;
        }
        if ($b_save) {$this->action_data->save();}
        return $this;
    }

    public function getGivenNamespace(): ?UserNamespace
    {
        /** @uses ActionDatum::data_namespace() */
        return $this->action_data->data_namespace;
    }

    public function setGivenNamespace(null|UserNamespace|string $what, bool $b_save = false): static
    {
        if ($what instanceof UserNamespace) {
            $this->action_data->data_namespace_id = $what->id;
        } else if ($what) {
            $this->action_data->data_namespace_id = UserNamespace::getThisNamespace(uuid: $what)->id;
        } else {
            $this->action_data->data_namespace_id = null;
        }
        if ($b_save) {$this->action_data->save();}
        return $this;
    }

    public function getOwningNamespace(): ?UserNamespace
    {
        /** @uses ActionDatum::data_owner_namespace() */
        return $this->action_data->data_owner_namespace;
    }



}


