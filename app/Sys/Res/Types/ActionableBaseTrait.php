<?php

namespace App\Sys\Res\Types;


use App\Exceptions\HexbatchCoreException;
use App\Models\ActionDatum;
use App\Models\UserNamespace;
use App\Sys\Collections\SystemTypes;
use BlueM\Tree;
use Carbon\Carbon;
use Hexbatch\Things\Enums\TypeOfThingStatus;
use Hexbatch\Things\Interfaces\IThingAction;
use Hexbatch\Things\Interfaces\IThingOwner;
use Hexbatch\Things\Models\Thing;
use Hexbatch\Things\Models\ThingHook;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

trait ActionableBaseTrait
{

    protected ?TypeOfThingStatus $status = null;
    protected array $post_events_to_send = [];

    public function getActionData() : ?ActionDatum { return $this->action_data;}

    protected function update_data_key(string $key,array $data) : bool {

        if (in_array($key ,['is_sending_events','is_system_privilege']) ) {
            if($this->action_data->$key !== $data[$key]) {
                $this->action_data->$key = $data[$key];
                return true;
            }
        }
        if (array_key_exists($key,$data) && !empty($data[$key]) && isset($this->$key)) {
            if ($this->$key !== $data[$key]) {
                $this->$key = $data[$key];
                $this->action_data->collection_data[$key] = $data[$key];
                return true;
            }
        }
        return false;
    }

    public function getDataOwner() : ?UserNamespace {
        /** @uses ActionDatum::data_owner_namespace() */
        return $this->action_data->data_owner_namespace;
    }

    protected function initData(bool $b_save = true) : ActionDatum {
        $root_id = null;
        $owner_id = null;
        if ($this->parent_action_data) {
            $root_id = $this->parent_action_data->root_data_id? : $this->parent_action_data->id;
        }
        if ($this->owner_namespace) {
            $owner_id = $this->owner_namespace->id;
        } elseif ($this->parent_action_data) {
            $owner_id = $this->parent_action_data->data_namespace_owner_id;
        }

        $async_flag = true;
        if ($this->is_async === null) {
            if ($this->parent_action_data?->is_async !== null) {$async_flag = $this->parent_action_data->is_async; }
        } else {
            $async_flag = $this->is_async;
        }
        $this->action_data = new ActionDatum();
        $this->action_data->is_async = $async_flag;
        $this->action_data->data_tags = $this->tags;
        $this->action_data->is_system_privilege = $this->is_system;
        $this->action_data->is_sending_events = $this->send_event;
        $this->action_data->root_data_id = $root_id;
        $this->action_data->parent_data_id = $this->parent_action_data?->id;
        $this->action_data->data_namespace_owner_id = $owner_id;
        $this->action_data->collection_data =$this->getInitialConstantData();
        $this->action_data->data_type_owner_id = $this->getType(b_construct_if_missing: false)?->id;

        if ($b_save || count(static::ACTIVE_COLLECTION_KEYS)) {
            $this->action_data->save();
            $this->action_data->refresh();
        }
        $this->saveCollectionKeys();
        return $this->action_data;
    }


    protected function setActionStatus(TypeOfThingStatus $status) {
        $this->action_data->action_status = $status;
        $this->action_data->save();
        $this->action_data->refresh();
        $this->wakeLinkedThings();
    }

    protected function getActionStatus() : TypeOfThingStatus { return $this->action_data->action_status;}




    public function getActionOwner(): ?IThingOwner {
        return $this->owner_namespace?:$this->action_data->data_owner_namespace;
    }

    public function isActionComplete(): bool
    {
        return in_array($this->getActionStatus(),TypeOfThingStatus::STATUSES_OF_COMPLETION);
    }

    public function isActionWaiting(): bool
    {
        return $this->getActionStatus() === TypeOfThingStatus::THING_WAITING;
    }

    public function getWaitTimeout() : ?int {
        return $this->action_data?->action_wait_timeout_seconds;
    }

    public function isActionSuccess(): bool
    {
        return $this->getActionStatus() === TypeOfThingStatus::THING_SUCCESS;
    }

    public function isActionFail(): bool
    {
        return in_array($this->getActionStatus(),[TypeOfThingStatus::THING_FAIL,TypeOfThingStatus::THING_ERROR]);

    }

    public function isActionError(): bool
    {
        return $this->getActionStatus() === TypeOfThingStatus::THING_ERROR;
    }

    public function getActionId(): int {return $this->action_data->id; }


    public function getActionUuid() : ?string {
        return $this->action_data?->ref_uuid;
    }

    public function getActionRef(): ?string
    {
        return static::UUID;
    }

    public function getActionName() : ?string {
        return static::getHexbatchClassName();
    }


    public function getActionType(): string
    {
        return static::getActionTypeStatic();
    }

    public static function getActionTypeStatic(): string
    {
        return static::class;
    }

    public function getChildrenTree(): ?Tree {return null;}



    public function getStartAt(): ?Carbon
    {
        return null;
    }

    public function getInvalidAt(): ?Carbon
    {
        return null;
    }

    public function isAsync(): bool
    {
        return $this->action_data?->is_async??true;
    }

    public function getActionTags(): array { return $this->action_data->data_tags->getArrayCopy()??[];}

    public function getRenderHtml(): ?string {return null;}


    /**
     * @return Collection|Thing[]
     */
    public function  getLinkedThings() : Collection|array
    {
        if (!$this->getActionType() || !$this->getActionId()) {return [];}
        /** @var Thing[] $what */
        return  Thing::buildThing(action_type_id: $this->getActionId(), action_type: $this->getActionType())->get();
    }

    public function wakeLinkedThings() : void
    {
        foreach ($this->getLinkedThings() as $thung) {
            try {
                $thung->continueThing();
            } catch (\Exception $e) {
                throw new \RuntimeException(message:"Could not wake thing ". $thung->ref_uuid, code: $e->getCode(),previous: $e);
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function runAction(array $data = []): void {
        try {
            $this->restoreData($data);
        } catch (\Exception $e) {
            Log::warning("could not restore data in ".static::class. " : ".$e->getMessage());
            $this->setActionStatus(TypeOfThingStatus::THING_ERROR);
            throw $e;
        }

        if ($this->isActionComplete()) {
            return;
        }

        try {
            $this->runActionInner(data: $data);
            if ($this->getActionStatus() === TypeOfThingStatus::THING_PENDING ) {
                //only set if not already changed from default
                $this->setActionStatus(TypeOfThingStatus::THING_SUCCESS);
            }


        }  catch (HexbatchCoreException $e) {
            $this->setActionStatus(TypeOfThingStatus::THING_FAIL);
            throw $e;
        }
        catch (\Exception $e) {
            $this->setActionStatus(TypeOfThingStatus::THING_ERROR);
            throw $e;
        }

        $this->postActionInner(data: $data);

    }






    public function getPreRunData(): array  {return [];}


    protected function restoreData(array $data = []) {
        if ($this->action_data) {
            $this->action_data->refresh();
            foreach (static::ACTIVE_DATA_KEYS as $key) {
                if ($this->action_data->collection_data->offsetExists($key)) {
                    $this->$key = $this->action_data->collection_data->offsetGet($key);
                }
            }
            $this->owner_namespace = $this->action_data->data_owner_namespace;
            $this->parent_action_data = $this->action_data->data_parent;
            $this->is_system = $this->action_data->is_system_privilege;
            $this->is_async = $this->action_data->is_async;
            $this->send_event = $this->action_data->is_sending_events;
            $this->tags = $this->action_data->data_tags->getArrayCopy()??[] ;
        }

        foreach (static::ACTIVE_DATA_KEYS as $key) {
            if(isset($data[$key])) { $this->$key = $data[$key];}
        }

        $this->restoreCollectionKeys();
    }



    public function getActionResult(): array
    {
        return $this->getMyData();
    }


    public function getDataSnapshot(): array
    {
        return $this->getMyData();
    }



    protected function saveCollectionKeys() {
        foreach (static::ACTIVE_COLLECTION_KEYS as $property_name => $class_or_array) {
            static::getClassAndPartition(class_or_array: $class_or_array,class: $class,partition: $partition);
            $this->action_data->addUuidsToCollection(class: $class,uuids: $this->$property_name,partition_flag: $partition,b_check_and_throw: true);
        }
    }

    protected function restoreCollectionKeys() {
        foreach (static::ACTIVE_COLLECTION_KEYS as $property_name => $class_or_array) {
            static::getClassAndPartition(class_or_array: $class_or_array,class: $class,partition: $partition);
            $this->$property_name = $this->action_data->getUuidsFromCollection(class: $class,partition_flag: $partition);
        }
    }

    private static function getClassAndPartition(string|array $class_or_array,?string &$class,?int &$partition) {
        $partition = 0;
        if (is_array($class_or_array)) {
            $class = $class_or_array['class']??null;
            if (!$class) { throw new \LogicException("class subkey not set for partitioning");}
            if (array_key_exists('partition',$class_or_array)) {
                if ($class_or_array['partition'] === null) {
                    $partition = null;
                } else {
                    $partition = (int)$class_or_array['partition'];
                }
            }
        } else {
            $class = $class_or_array;
        }
    }



    public function getInitialConstantData(): ?array {
        $ret = [];
        foreach (static::ACTIVE_DATA_KEYS as $key) {
            $ret[$key] = $this->$key;
        }

        $ret['is_sending_events'] = $this->send_event;
        $ret['is_system_privilege'] = $this->is_system;

        return $ret;

    }

    public function setChildActionResult(IThingAction $child): void {

    }

    public function getMoreSiblingActions(): array {
        if ($this->isActionSuccess()) {
            return $this->post_events_to_send;
        }
        return [];
    }

    public function addDataBeforeRun(array $data): void
    {
        $b_changed = false;
        foreach (array_keys($this->getInitialConstantData()??[]) as $data_key) {
            $b_changed = ($b_changed || $this->update_data_key($data_key,$data) );
        }
        if($b_changed) { $this->action_data->save();}
    }

    /**
     * @var array<int,IThingAction|static>
     */
    static array $data_cache = [];



    public static function resolveAction(int $action_id): IThingAction {
        if (array_key_exists($action_id,static::$data_cache)) {
            return static::$data_cache[$action_id];
        }


        /** @var BaseType $system_type */
        $system_type = SystemTypes::getTypeByUuid(static::UUID);
        if (!$system_type) {throw new \InvalidArgumentException("cannot resolve action for command by id of $action_id");}

        /** @var ActionDatum $data */
        $data = ActionDatum::buildHexbatchData(me_id: $action_id)->first();

        $ret =  new $system_type(action_data:$data);
        static::$data_cache[$action_id] = $ret;
        return $ret;
    }

    public static function resolveActionFromUiid(string $uuid) : IThingAction
    {
        if (array_key_exists($uuid,static::$data_cache)) {
            return static::$data_cache[$uuid];
        }

        /** @var BaseType $system_type */
        $system_type = SystemTypes::getTypeByUuid(static::UUID);
        if (!$system_type) {throw new \InvalidArgumentException("cannot resolve action for command by uuid of $uuid");}

        /** @var ActionDatum $data */
        $data = ActionDatum::buildHexbatchData(uuid: $uuid)->first();

        $ret =  new $system_type(action_data:$data);
        static::$data_cache[$uuid] = $ret;
        return $ret;
    }

    public static function registerAction(): void
    {
        Thing::registerActionType(static::class);
        ThingHook::registerActionType(static::class);
    }



}
