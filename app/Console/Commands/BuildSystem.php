<?php

namespace App\Console\Commands;

use App\Sys\Build\LoadStatic;
use App\Sys\Build\Mappers\ActionMapper;
use App\Sys\Build\Mappers\ApiMapper;
use App\Sys\Build\Mappers\AttributeMapper;
use App\Sys\Build\SystemResources;
use App\Sys\Res\Atr\ISystemAttribute;
use App\Sys\Res\Sets\ISystemSet;
use App\Sys\Res\Types\ISystemType;
use App\Sys\Res\Types\Stk\Root\Evt;
use Illuminate\Console\Command;

class BuildSystem extends Command
{
    /**
     *
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hex:build {--check} {--list} {--list-attributes} {--list-types} {--list-elements} {--list-sets} '.
                                        ' {--list-users} {--list-servers} {--list-namespaces} {--mapper} ' .
                                        ' {--show-current } {--show-old} {--show-new} {--build} {--trim-old} {--show-events} {--show-unused-events} '
    ;


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

            $this->warn("Attribute ".$attr_class::getHexbatchClassName()." $uuid is duplicate claimed by type".$type_class::getHexbatchClassName());
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

        if ($this->option('mapper')) {
            $this->info("Writing actions to ". ActionMapper::getOutputPath());
            ActionMapper::writeToStandardFile();
            $this->info("Writing api to ". ActionMapper::getOutputPath());
            ApiMapper::writeToStandardFile();

            $this->info("Writing attributes to ". AttributeMapper::getOutputPath());
            AttributeMapper::writeToStandardFile();
            $this->info("Done");
            return 0;
        }

        if ($this->option('check')) {
            $number_actions = 0;
            $number_api = 0;
            $number_events = 0;
            $number_meta = 0;

            $number_metadata = 0;
            foreach ($load->types as  $val) {
                $full_class_type_name = $val;
                if (is_subclass_of($full_class_type_name, 'App\Sys\Res\Types\Stk\Root\Act\BaseAction') ) { $number_actions++;}
                if (is_subclass_of($full_class_type_name, 'App\Sys\Res\Types\Stk\Root\Api') ) { $number_api++;}
                if (is_subclass_of($full_class_type_name, 'App\Sys\Res\Types\Stk\Root\Event') ) { $number_events++;}
                if (is_subclass_of($full_class_type_name, 'App\Sys\Res\Types\Stk\Root\Meta') ) { $number_meta++;}

            }
            foreach ($load->attributes as  $full_class_attribute) {
                if (is_subclass_of($full_class_attribute, 'App\Sys\Res\Atr\Stk\MetaData\Metadata') ) { $number_metadata++;}
            }
            $this->info('UUID '.count($load->uuid_classes));
            $this->info('TYPES '.count($load->type_name_uuids));
            $this->info('    Actions '.$number_actions);
            $this->info('    Api '.$number_api);
            $this->info('    Events '.$number_events);
            $this->info('    Meta '.$number_meta);
            $this->info('    Others '.count($load->type_name_uuids) - $number_actions - $number_api - $number_events - $number_meta);
            $this->info('ATTRIBUTES '.count($load->attribute_name_uuids));
            $this->info('    Meta '.$number_metadata);
            $this->info('SETS '.count($load->type_sets));
            $this->info('ELEMENTS '.count($load->type_elements));
            $this->info('SERVERS '.count($load->system_servers));
            $this->info('NAMESPACES '.count($load->namespaces));
            $this->info('USERS '.count($load->system_users));
            $this->info('All '.count($load->loaded_classes));


        }


        if ($this->option('show-events')) {
            $data = [];
            $type_names = array_values($load->types);
            sort($type_names);

            foreach ($type_names as $full_type_name ) {
                if (!is_subclass_of($full_type_name, 'App\Sys\Res\Types\Stk\Root\Act\BaseAction') ) { continue; }
                $data[] = [$full_type_name::getHexbatchClassName(),implode("\n",$full_type_name::getRelatedEvents())];
            }

            $this->table(['Type Name','Events'],$data);
        }

        if ($this->option('show-unused-events')) {
            $used_events = [];
            $all_events_maybe = [];
            $all_events = [];
            $type_names = array_values($load->types);
            sort($type_names);

            foreach ($type_names as $full_type_name ) {
                if (is_subclass_of($full_type_name, 'App\Sys\Res\Types\Stk\Root\Act\BaseAction') ) {
                    $used_events = array_merge($full_type_name::getRelatedEvents(), $used_events);
                }
                if (is_subclass_of($full_type_name, 'App\Sys\Res\Types\Stk\Root\Evt\BaseEvent') ) {
                    $all_events_maybe[] = $full_type_name;
                }
            }

            $structure_only = [
                Evt\ScopeElement::class,
                Evt\ScopeType::class,
                Evt\ScopeSet::class,
                Evt\ScopeElsewhere::class,
                Evt\ScopeServer::class,
                Evt\Server\Nothing::class
            ];
            foreach ($all_events_maybe as $maybe_event) {
                if (in_array($maybe_event,$structure_only)) {continue;}
                $all_events[] = $maybe_event;
            }
            //filter out not real events

            $diff = array_diff($all_events,$used_events);
            $show_array = [];
            foreach ($diff as $not_used) {
                $show_array[] = [$not_used];
            }
            $this->table(['Event'],$show_array);
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
                        $type_name = $type_class::getHexbatchClassName();
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
                            "\n  Element ". $element_in_set::getClassUuid() .
                            "\n  Type    ". $element_in_set::getSystemTypeClass()::getClassUuid() .
                            "\n   ". $element_in_set::getSystemTypeClass()::getFlatInheritance();
                    }
                    $set_name =
                         "\n Set     ".$set_here::getClassUuid()
                        ."\n Type    ". $set_here::getDefiningSystemElementClass()::getSystemTypeClass()::getClassUuid()
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
                    $some_user::getUserName(),$some_user::getUserPassword(),$some_user::getClassUuid()
                    ,
                    " Type    ". $type_class::getClassUuid()
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
                    $some_server::getClassUuid()
                    ,
                    " Type    ". $type_class::getClassUuid()
                    . "\n   ".$type_class::getFlatInheritance()
                ];

            }

            $this->table(['Domain','Name','Uuid','Type'],$data);
        }

        if ($this->option('list')|| $this->option('list-namespaces')) {
            $data = [];
            foreach ($load->namespaces as $some_namespace) {

                $dets =  $some_namespace::getNamespaceName()
                    . " NS    ". $some_namespace::getClassUuid()
                    . " \n ".
                    $some_namespace::getSystemServerClass()::getServerName()
                    . " Server    ". $some_namespace::getSystemServerClass()::getClassUuid()
                    . " \n ".
                    $some_namespace::getSystemUserClass()::getUserName()
                    . " User    ". $some_namespace::getSystemUserClass()::getClassUuid();


                if($some_namespace::getSystemTypeClass()::getSystemHandleElementClass()) {
                    $handle =
                      "\n   Handle Element " . $some_namespace::getSystemTypeClass()::getSystemHandleElementClass()::getClassUuid()

                    . "\n   Handle Type    ". $some_namespace::getSystemTypeClass()::getSystemHandleElementClass()::getSystemTypeClass()::getClassUuid()
                      ."\n   ". $some_namespace::getSystemTypeClass()::getSystemHandleElementClass()::getSystemTypeClass()::getHexbatchClassName()

                    . "\n   ".$some_namespace::getSystemTypeClass()::getSystemHandleElementClass()::getSystemTypeClass()::getFlatInheritance();

                    $dets .= $handle;
                }

                $data[] = [
                    $dets
                    ,

                    " Type    ". $some_namespace::getSystemTypeClass()::getClassUuid() ."\n   ". $some_namespace::getSystemTypeClass()::getHexbatchClassName()
                    . "\n   ".$some_namespace::getSystemTypeClass()::getFlatInheritance()
                    ." Home Set   ". $some_namespace::getSystemHomeClass()::getClassUuid() ."\n   ". $some_namespace::getSystemTypeClass()::getHexbatchClassName()
                    . "\n   ".$some_namespace::getSystemHomeClass()::getDefiningSystemElementClass()::getSystemTypeClass()::getFlatInheritance()

                    ,


                     " Public    ". $some_namespace::getSystemPublicClass()::getClassUuid() ."\n   ". $some_namespace::getSystemPublicClass()::getSystemTypeClass()::getHexbatchClassName()
                    . "\n   ".$some_namespace::getSystemPublicClass()::getSystemTypeClass()::getFlatInheritance()

                    . " \n "
                    ." Private    ". $some_namespace::getSystemPrivateClass()::getClassUuid() ."\n   ". $some_namespace::getSystemPrivateClass()::getSystemTypeClass()::getHexbatchClassName()
                    . "\n   ".$some_namespace::getSystemPrivateClass()::getSystemTypeClass()::getFlatInheritance()
                    ."\n   "
                    ,
                ];

            }

            $this->table(['Name|Server|User|Handle','Type|Home','Public|Private'],$data);
        }

        if ($this->option('show-new')) {
            $oldly = SystemResources::showNew();
            $data = [];
            foreach ($oldly as $build_type => $new_classes ) {
                foreach ($new_classes as  $new_class) {
                    $data[] = [
                        $build_type,
                        $new_class::getClassUuid(),
                        $new_class::getHexbatchClassName()
                    ];
                }
            }
            if (empty($data)) {
                $this->info("No new");
            } else {
                $this->table(['Category','Uuid','Name'],$data);
            }
        }



        if ($this->option('show-current')) {
            $curry = SystemResources::showCurrent();
            $data = [];
            foreach ($curry as $build_type => $things_current ) {
                foreach ($things_current as $currently) {
                    $data[] = [
                        $build_type,
                        $currently->getUuid(),
                        $currently->getName(),
                    ];
                }
            }
            if (empty($data)) {
                $this->info("No Current");
            } else {
                $this->table(['Category','Uuid','Name'],$data);
            }
        }

        if ($this->option('show-old')) {
            $oldly = SystemResources::showOld();
            $data = [];
            foreach ($oldly as $build_type => $things_old ) {
                foreach ($things_old as $elderly) {
                    $data[] = [
                        $build_type,
                        $elderly->getUuid(),
                        $elderly->getName(),
                    ];
                }
            }
            if (empty($data)) {
                $this->info("No old");
            } else {
                $this->table(['Category','Uuid','Name'],$data);
            }
        }



        if ($this->option('build')) {
            $newly = SystemResources::build();
            $data = [];
            foreach ($newly as $build_type => $things_made ) {
                foreach ($things_made as $something_made) {
                    $data[] = [
                       $build_type,
                       $something_made::getClassUuid(),
                       $something_made::getHexbatchClassName(),
                    ];
                }
            }
            if (empty($data)) {
                $this->info("Nothing created");
            } else {
                $this->table(['Category','Uuid','Name'],$data);
            }


        }

        if ($this->option('trim-old')) {
            $oldly = SystemResources::removeOld();
            $data = [];
            foreach ($oldly as $build_type => $things_old ) {
                foreach ($things_old as $elderly) {
                    $data[] = [
                        $build_type,
                        $elderly->getUuid(),
                        $elderly->getName(),
                    ];
                }
            }
            if (empty($data)) {
                $this->info("Did not remove any old");
            } else {
                $this->table(['Category','Uuid','Name'],$data);
            }
        }





        return 0;
    }
}
