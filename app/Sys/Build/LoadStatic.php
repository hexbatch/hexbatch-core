<?php

namespace App\Sys\Build;

use App\Helpers\Utilities;
use App\Sys\Res\Atr\ISystemAttribute;
use App\Sys\Res\Ele\ISystemElement;
use App\Sys\Res\Namespaces\ISystemNamespace;
use App\Sys\Res\Servers\ISystemServer;
use App\Sys\Res\Sets\ISystemSet;
use App\Sys\Res\Types\ISystemType;
use App\Sys\Res\Users\ISystemUser;

class LoadStatic
{

    public array $bad_uuid = [];
    public array $non_uuid = [];
    public array $repeating_names = [];

    public array $repeat_type_names = [];
    public array $bad_type_name = [];
    public array $type_name_uuids = [];
    public array $type_tree_uuids = [];
    public array $types = [];


    public array $bad_attribute_name = [];
    public array $repeat_attribute_names = [];
    public array $repeating_attribute_ownership = [];
    public array $attribute_name_uuids = [];
    public array $attributes = [];
    public array $attribute_uuid_classes = [];
    public array $attribute_type_classes = [];

    /**
     * @var array<string,ISystemElement[]> $type_elements
     */
    public array $type_elements = [];

    /**
     * @var array<string,ISystemSet[]> $type_sets
     */
    public array $type_sets = [];

    /**
     * @var ISystemSet[] $sets
     */
    public array $sets = [];

    /**
     * @var ISystemElement[] $elements
     */
    public array $elements = [];


    /**
     * @var ISystemNamespace[] $namespaces
     */
    public array $namespaces = [];


    /**
     * @var ISystemServer[] $system_servers
     */
    public array $system_servers = [];

    /**
     * @var ISystemUser[] $system_users
     */
    public array $system_users = [];



    public array $uuid_classes = [];
    public array $class_uuids = [];
    public array $loaded_classes = [];

    public function __construct()
    {
        $this->doLoad();
    }


    protected function doLoad() {
        $this->loaded_classes = SystemResources::loadClasses();
        $this->uuid_classes = SystemResources::getUuidDictionary();
        $this->class_uuids = array_flip($this->uuid_classes);


        foreach ($this->class_uuids as  $uuid) {
            if (!Utilities::is_uuid($uuid)) {
                $this->bad_uuid[] = $uuid;
            }
        }

        $this->non_uuid = [];
        foreach ($this->loaded_classes as $who) {
            if (!isset($this->class_uuids[$who])) {
                $this->non_uuid[] = $who;

            }
        }


         $type_names = [];
         $attribute_names = [];

        foreach ($this->class_uuids as $full_class_name => $uuid) {
            $interfaces = class_implements($full_class_name);

            if (isset($interfaces['App\Sys\Res\Types\ISystemType'])) {
                /**
                 * @type ISystemType $full_class_name
                 */
                $name = $full_class_name::getClassName();
                if (!Utilities::isValidResourceName($name)) {
                    $this->bad_type_name[$uuid] = $name;

                }
                if (isset($type_names[$name])) {
                    $this->repeat_type_names[$uuid] = $name;
                }

                if (isset($this->repeating_names[$name])) {
                    $this->repeating_names[$name] = $uuid;
                }
                $type_names[$name] = true;

                $this->type_name_uuids[ $name] = $uuid;
                $this->types[ $uuid] = $full_class_name;
                $this->type_tree_uuids[$full_class_name::getFlatInheritance() ] = $uuid ;

                /** @type ISystemAttribute[]|string[] $attr */
                $attr = $full_class_name::getAttributeClasses();
                foreach ($attr as $attr_class) {
                    if (isset($attribute_type_classes[$attr_class])) {
                        $this->repeating_attribute_ownership[ $attr_class::getClassUuid()] = [
                            'attribute_class'=>$attr_class,
                            'type_class' => $full_class_name
                        ];
                    }

                    $this->attribute_type_classes[$attr_class] = $full_class_name;
                }
            }
            else if (isset($interfaces['App\Sys\Res\Atr\ISystemAttribute'])) {
                /**
                 * @type ISystemAttribute $full_class_name
                 */
                $name = $full_class_name::getClassName();
                if (!Utilities::isValidResourceName($name)) {
                    $this->bad_attribute_name[$uuid] = $name;

                }
                if (isset($attribute_names[$name])) {
                    $this->repeat_attribute_names[$uuid] = $name;
                }

                if (isset($this->repeating_names[$name])) {
                    $this->repeating_names[$name] = $uuid;
                }
                $attribute_names[$name] = true;
                $this->attribute_uuid_classes[$uuid] = $full_class_name;
                $this->attribute_name_uuids[$full_class_name::getChainName() ] =$uuid ;
                $this->attributes[] = $full_class_name;
            }

            else if (isset($interfaces['App\Sys\Res\Ele\ISystemElement'])) {
                /**
                 * @type ISystemElement $full_class_name
                 * @var ISystemType $element_type_class
                 */
                $element_type_class = $full_class_name::getSystemTypeClass();

                $element_type_uuid = $element_type_class::getClassUuid();
                if (!isset($this->type_elements[$element_type_uuid])) { $this->type_elements[$element_type_uuid] = [];}
                $this->type_elements[$element_type_uuid][] = $full_class_name;
                $this->elements[] = $full_class_name;
            }

            else if (isset($interfaces['App\Sys\Res\Sets\ISystemSet'])) {
                /**
                 * @type ISystemSet $full_class_name
                 * @var ISystemElement $element_class
                 */
                $element_class = $full_class_name::getDefiningSystemElementClass();

                /**
                 * @var ISystemType $element_type_class
                 */
                $element_type_class = $element_class::getSystemTypeClass();


                $element_type_uuid = $element_type_class::getClassUuid();
                if (!isset($this->type_sets[$element_type_uuid])) { $this->type_sets[$element_type_uuid] = [];}
                $this->type_sets[$element_type_uuid][] = $full_class_name;
                $this->sets[] = $full_class_name;
            }


            else if (isset($interfaces['App\Sys\Res\Users\ISystemUser'])) {
                /**
                 * @type ISystemUser $full_class_name
                 */
                $this->system_users[] = $full_class_name;
            }

            else if (isset($interfaces['App\Sys\Res\Servers\ISystemServer'])) {
                /**
                 * @type ISystemServer $full_class_name
                 */
                $this->system_servers[] = $full_class_name;
            }

            else if (isset($interfaces['App\Sys\Res\Namespaces\ISystemNamespace'])) {
                /**
                 * @type ISystemNamespace $full_class_name
                 */

                $this->namespaces[] = $full_class_name;
            }
        }
    }

}
