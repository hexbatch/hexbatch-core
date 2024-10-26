<?php

namespace App\Console\Commands;

use App\Sys\Build\LoadStatic;
use App\Sys\Res\Atr\ISystemAttribute;
use App\Sys\Res\Sets\ISystemSet;
use App\Sys\Res\Types\ISystemType;
use Illuminate\Console\Command;

class BuildSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hbc:system {--check} {--list} {--list-attributes} {--list-types} {--list-elements} {--list-sets} '.
                                        ' {--list-users} {--list-servers} {--list-namespaces} '
    ;
    /*
     *
     */

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manages system defined resources';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle()
    {

        $load = new LoadStatic();
        foreach ($load->bad_uuid as $bad_uuid) {
            $this->warn('malformed uuid '. $bad_uuid);
        }
        foreach ($load->non_uuid as $non_uuid) {
            $this->warn('no uuid given '. $non_uuid);
        }
        foreach ($load->bad_type_name as $uuid => $bad_name) {
            $this->warn("bad type name $bad_name for uuid $uuid ");
        }

        foreach ($load->repeat_type_names as $uuid => $repeat_type_name) {
            $this->warn("duplicate type name $repeat_type_name for uuid $uuid ");
        }

        foreach ($load->bad_attribute_name as $uuid => $bad_attr_name) {
            $this->warn("bad attribute name $bad_attr_name for uuid $uuid ");
        }

        foreach ($load->repeat_attribute_names as $uuid => $repeat_attr_name) {
            $this->warn("duplicate attribute name $repeat_attr_name for uuid $uuid ");
        }

        foreach ($load->repeating_names as $repeating_name) {
            $this->warn("duplicate name in system $repeating_name ");
        }

        foreach ($load->repeating_attribute_ownership as $uuid => $info) {
            /**
             * @type ISystemType $type_class
             */
            $type_class = $info['type_class'];

            /**
             * @type ISystemAttribute $attr_class
             */
            $attr_class = $info['attribute_class'];

            $this->warn("Attribute ".$attr_class::getName()." $uuid is duplicate claimed by type".$type_class::getName());
        }



        if (
            count($load->non_uuid) || count($load->bad_uuid) || count($load->bad_type_name) || count($load->bad_attribute_name)
            || count($load->repeat_attribute_names) || count($load->repeat_type_names) || count($load->repeating_names)
            || count($load->repeating_attribute_ownership)
        ) {
            if (count($load->non_uuid)) {
                $this->info('Non UUID '. count($load->non_uuid));
            }

            if (count($load->bad_uuid)) {
                $this->info('Bad UUID '. count($load->bad_uuid));
            }

            if (count($load->bad_type_name)) {
                $this->info('Bad Type Name '. count($load->bad_type_name));
            }

            if (count($load->bad_attribute_name)) {
                $this->info('Bad Attribute Name '. count($load->bad_attribute_name));
            }

            if (count($load->repeat_type_names)) {
                $this->info('Repeating Type Name '. count($load->repeat_type_names));
            }

            if (count($load->repeat_attribute_names)) {
                $this->info('Repeating Attribute Name '. count($load->repeat_attribute_names));
            }

            if (count($load->repeating_names)) {
                $this->info('Repeating Names '. count($load->repeating_names));
            }

            if (count($load->repeating_attribute_ownership)) {
                $this->info('Repeat Ownership Attribute '. count($load->repeating_attribute_ownership));
            }

            return 1;
        }

        if ($this->option('check')) {

            $this->info('UUID '.count($load->uuid_classes));
            $this->info('TYPES '.count($load->type_name_uuids));
            $this->info('ATTRIBUTES '.count($load->attribute_name_uuids));
            $this->info('SETS '.count($load->type_sets));
            $this->info('ELEMENTS '.count($load->type_elements));
            $this->info('SERVERS '.count($load->system_servers));
            $this->info('NAMESPACES '.count($load->namespaces));
            $this->info('USERS '.count($load->system_users));
            $this->info('All '.count($load->loaded_classes));

        }

        if ($this->option('list')|| $this->option('list-attributes')) {
            $data = [];
            $names = array_keys($load->attribute_name_uuids);
            sort($names);
            foreach ($names as $name) {
                $uuid = $load->attribute_name_uuids[$name];
                $type_name = null;
                $attribute_class = $load->attribute_uuid_classes[$uuid] ?? null;



                if ($attribute_class) {
                    /** @type ISystemType $type_class */
                    $type_class = $load->attribute_type_classes[$attribute_class]??null;
                    if ($type_class) {
                        $type_name = $type_class::getName();
                    }
                }
                $data[] = [$name, $uuid, $type_name];
            }
            $this->table(['Attribute Name', 'Uuid', 'Owning Type'], $data);
        }

        if ($this->option('list')|| $this->option('list-types')) {
            $data = [];
            $names = array_keys($load->type_tree_uuids);
            sort($names);
            foreach ($names as $name ) {
                $uuid = $load->type_tree_uuids[$name];
                $data[] = [$name,$uuid];
            }

            $this->table(['Type Name','Uuid'],$data);
        }

        if ($this->option('list')|| $this->option('list-elements')) {
            $sorter = [];
            foreach ($load->type_elements as $type_guid => $element_array) {
                /** @var ISystemType $type_class */
                $type_class = $load->uuid_classes[$type_guid];

                $sorter[$type_class::getFlatInheritance()] = $element_array;
            }
            $sorter_names = array_keys($sorter);
            sort($sorter_names);
            $data = [];
            foreach ($sorter_names as $name ) {
                foreach ($sorter[$name] as $element_here) {
                    $data[] = [$name,$element_here];
                }

            }

            $this->table(['Type Name','Elements'],$data);
        }

        if ($this->option('list')|| $this->option('list-sets')) {
            $sorter = [];
            foreach ($load->type_sets as $type_guid => $set_array) {
                /** @var ISystemType $type_class */
                $type_class = $load->uuid_classes[$type_guid];

                $sorter[$type_class::getFlatInheritance()] = $set_array;
            }
            $sorter_names = array_keys($sorter);
            sort($sorter_names);
            $data = [];
            foreach ($sorter_names as $name ) {
                /**
                 * @var ISystemSet $set_here
                 */
                foreach ($sorter[$name] as $set_here) {

                    $set_content_array = [];
                    foreach ($set_here::getMemberSystemElementClasses() as $element_in_set) {
                        $set_content_array[] =
                            "\n  Element ". $element_in_set::getUuid() .
                            "\n  Type    ". $element_in_set::getSystemTypeClass()::getUuid() .
                            "\n   ". $element_in_set::getSystemTypeClass()::getFlatInheritance();
                    }
                    $set_name =
                         "\n Set     ".$set_here::getUuid()
                        ."\n Type    ". $set_here::getDefiningSystemElementClass()::getSystemTypeClass()::getUuid()
                        . "\n   ". $name;
                    $events =  ' '. "\n ".($set_here::hasEvents()?'Yes':'No' );
                    $data[] = [$set_name,$events,implode("\n",$set_content_array)];
                }

            }

            $this->table(['Set Type','Events','Element Members'],$data);
        }

        if ($this->option('list')|| $this->option('list-users')) {
            $data = [];
            foreach ($load->system_users as $some_user ) {

                $type_class = $some_user::getSystemNamespaceClass()::getSystemTypeClass();

                $data[] = [
                    $some_user::getUserName(),$some_user::getUserPassword(),$some_user::getUuid()
                    ,
                    " Type    ". $type_class::getUuid()
                    . "\n   ".$type_class::getFlatInheritance()
                ];

            }

            $this->table(['Username','Password','Uuid','Type'],$data);
        }

        if ($this->option('list')|| $this->option('list-servers')) {
            $data = [];
            foreach ($load->system_servers as $some_server ) {

                $type_class = $some_server::getSystemNamespaceClass()::getSystemTypeClass();

                $data[] = [
                    $some_server::getServerDomain(),
                    $some_server::getServerName(),
                    $some_server::getUuid()
                    ,
                    " Type    ". $type_class::getUuid()
                    . "\n   ".$type_class::getFlatInheritance()
                ];

            }

            $this->table(['Domain','Name','Uuid','Type'],$data);
        }

        if ($this->option('list')|| $this->option('list-namespaces')) {
            $data = [];
            foreach ($load->namespaces as $some_namespace) {

                $dets =  $some_namespace::getNamespaceName()
                    . " NS    ". $some_namespace::getUuid()
                    . " \n ".
                    $some_namespace::getSystemServerClass()::getServerName()
                    . " Server    ". $some_namespace::getSystemServerClass()::getUuid()
                    . " \n ".
                    $some_namespace::getSystemUserClass()::getUserName()
                    . " User    ". $some_namespace::getSystemUserClass()::getUuid();


                if($some_namespace::getSystemTypeClass()::getSystemHandleElementClass()) {
                    $handle =
                      "\n   Handle Element " . $some_namespace::getSystemTypeClass()::getSystemHandleElementClass()::getUuid()

                    . "\n   Handle Type    ". $some_namespace::getSystemTypeClass()::getSystemHandleElementClass()::getSystemTypeClass()::getUuid()
                      ."\n   ". $some_namespace::getSystemTypeClass()::getSystemHandleElementClass()::getSystemTypeClass()::getName()

                    . "\n   ".$some_namespace::getSystemTypeClass()::getSystemHandleElementClass()::getSystemTypeClass()::getFlatInheritance();

                    $dets .= $handle;
                }

                $data[] = [
                    $dets
                    ,

                    " Type    ". $some_namespace::getSystemTypeClass()::getUuid() ."\n   ". $some_namespace::getSystemTypeClass()::getName()
                    . "\n   ".$some_namespace::getSystemTypeClass()::getFlatInheritance()
                    ." Home Set   ". $some_namespace::getSystemHomeClass()::getUuid() ."\n   ". $some_namespace::getSystemTypeClass()::getName()
                    . "\n   ".$some_namespace::getSystemHomeClass()::getDefiningSystemElementClass()::getSystemTypeClass()::getFlatInheritance()

                    ,


                     " Public    ". $some_namespace::getSystemPublicClass()::getUuid() ."\n   ". $some_namespace::getSystemPublicClass()::getSystemTypeClass()::getName()
                    . "\n   ".$some_namespace::getSystemPublicClass()::getSystemTypeClass()::getFlatInheritance()

                    . " \n "
                    ." Private    ". $some_namespace::getSystemPrivateClass()::getUuid() ."\n   ". $some_namespace::getSystemPrivateClass()::getSystemTypeClass()::getName()
                    . "\n   ".$some_namespace::getSystemPrivateClass()::getSystemTypeClass()::getFlatInheritance()
                    ."\n   "
                    ,
                ];

            }

            $this->table(['Name|Server|User|Handle','Type|Home','Public|Private'],$data);
        }

        /*
         * todo need tasks
         *   :build
         *   :list current
         *   :list_new
         *   :list_old
         */

        return 0;
    }
}
