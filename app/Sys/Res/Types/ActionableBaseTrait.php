<?php

namespace App\Sys\Res\Types;


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

    protected function initData(bool $b_save = true) : ActionDatum {
        $this->action_data = new ActionDatum();
        $this->action_data->is_system_privilege = $this->is_system;
        $this->action_data->is_sending_events = $this->send_event;
        $this->action_data->root_data_id = $this->action_data_root_id;
        $this->action_data->parent_data_id = $this->action_data_parent_id;
        $this->action_data->collection_data =$this->getInitialConstantData();
        $this->action_data->data_action_type_id = $this->getType()->id;
        if ($b_save || count(static::ACTIVE_COLLECTION_KEYS)) {
            $this->action_data->save();
        }
        $this->saveCollectionKeys();
        return $this->action_data;
    }

    protected function setActionStatus(TypeOfThingStatus $status) {
        $this->action_data->action_status = $status;
        $this->action_data->save();
    }

    protected function getActionStatus() : TypeOfThingStatus { return $this->action_data->action_status;}

    public function __construct(
        protected ?ActionDatum   $action_data = null,
        protected ?int           $action_data_parent_id = null,
        protected ?int           $action_data_root_id = null,
        protected ?UserNamespace $owner = null,
        protected bool           $b_type_init = false,
        protected bool         $is_system = false,
        protected bool         $send_event = true,

    )
    {
        if ($this->b_type_init) {return;}
        if ($this->action_data) {  $this->restoreData(); } else {$this->initData();}
    }


    public function getActionOwner(): ?IThingOwner {
        return $this->owner;
    }

    public function isActionComplete(): bool
    {
        return in_array($this->getActionStatus(),TypeOfThingStatus::STATUSES_OF_COMPLETION);
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

    public function getActionRef(): ?string
    {
        return static::UUID;
    }

    public function getActionPriority(): int
    {
        return 0;
    }

    public function getActionType(): string
    {
        return static::getActionTypeStatic();
    }

    public static function getActionTypeStatic(): string
    {
        return static::TYPE_NAME;
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
        return false;
    }

    public function getActionTags(): array { return [static::getClassName()];}

    public function getRenderHtml(): ?string {return null;}



    public function runAction(array $data = []): void {
        $this->restoreData($data);

    }




    public function getPreRunData(): array  {return [];}


    protected function restoreData(array $data = []) {
        if ($this->action_data) {
            foreach (static::ACTIVE_DATA_KEYS as $key) {
                $this->$key = $this->action_data->collection_data->offsetGet($key);
            }
            $this->is_system = $this->action_data->is_system_privilege;
            $this->send_event = $this->action_data->is_sending_events;
        }

        foreach (static::ACTIVE_DATA_KEYS as $key) {
            if(isset($data[$key])) { $this->$key = $data[$key];}
        }

        $this->restoreCollectionKeys();
    }

    protected function getMyData() :array { return []; }

    public function getActionResult(): array
    {
        return $this->getMyData();
    }


    public function getDataSnapshot(): array
    {
        return $this->getMyData();
    }

    const array ACTIVE_COLLECTION_KEYS = [];

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

    private static function getClassAndPartition(string|array $class_or_array,string &$class,?int &$partition) {
        $partition = 0;
        if (is_array($class_or_array)) {
            $class = $class_or_array['class'];
            if (array_key_exists('partition',$class_or_array[])) {
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


    const array ACTIVE_DATA_KEYS = [];
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
        //todo make sure this is set to false, and short circuits if the child is an event that returns denied
    }

    public function getMoreSiblingActions(): array {
        return $this->post_events_to_send;
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

    public static function registerAction(): void
    {
        Thing::registerActionType(static::class);
        ThingHook::registerActionType(static::class);
    }
}
