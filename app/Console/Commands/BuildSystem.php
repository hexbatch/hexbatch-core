<?php

namespace App\Console\Commands;

use App\Helpers\Utilities;
use App\Sys\Res\Atr\ISystemAttribute;
use App\Sys\Res\Types\ISystemType;
use App\Sys\SystemResources;
use Illuminate\Console\Command;

class BuildSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hbc:system {--check} {--list}';
    /*
     *
     */

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'makes, updates and tools for the system types, attributes, elements, sets, namespaces and servers';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle()
    {


        $what = SystemResources::loadClasses();
        $uuid_classes = SystemResources::getUuidDictionary();
        $class_uuids = array_flip($uuid_classes);

        $bad_uuid = [];
        foreach ($class_uuids as  $uuid) {
           if (!Utilities::is_uuid($uuid)) {
               $bad_uuid[] = $uuid;
               $this->warn('malformed uuid '. $uuid);
           }
        }

        $non_uuid = [];
        foreach ($what as $who) {
            if (!isset($class_uuids[$who])) {
                $non_uuid[] = $who;
                $this->warn('no uuid given '. $who);
            }
        }

        $type_names = [];
        $attribute_names = [];
        $type_name_uuids = [];
        $attribute_name_uuids = [];
        $bad_name = [];
        $repeat_names = [];

        foreach ($class_uuids as $full_class_name => $uuid) {
            $interfaces = class_implements($full_class_name);

            if (isset($interfaces['App\Sys\Res\Types\ISystemType'])) {
                /**
                 * @type ISystemType $full_class_name
                 */
                $name = $full_class_name::getName();
                if (!Utilities::isValidResourceName($name)) {
                    $bad_name[] = $name;
                    $this->warn("bad type name $name for uuid $uuid ");
                }
                if (isset($type_names[$name])) {
                    $this->warn("duplicate type name $name for uuid $uuid ");
                    $repeat_names[] = $name;
                }
                $type_names[$name] = true;
                $type_name_uuids[$uuid] = $name;
            } else if (isset($interfaces['App\Sys\Res\Atr\ISystemAttribute'])) {
                /**
                 * @type ISystemAttribute $full_class_name
                 */
                $name = $full_class_name::getName();
                if (!Utilities::isValidResourceName($name)) {
                    $bad_name[] = $name;
                    $this->warn("bad attribute name $name for uuid $uuid ");
                }
                if (isset($attribute_names[$name])) {
                    $this->warn("duplicate attribute name $name for uuid $uuid ");
                    $repeat_names[] = $name;
                }
                $attribute_names[$name] = true;

                $attribute_name_uuids[$uuid] = $full_class_name::getChainName();
            }
        }



        if (count($non_uuid) || count($bad_uuid) || count($bad_name)|| count($repeat_names)) {
            $this->info('Non UUID '. count($non_uuid));
            $this->info('Bad UUID '. count($bad_uuid));
            $this->info('Bad Name '. count($bad_name));
            $this->info('Repeat Name '. count($repeat_names));
            return 1;
        }

        if ($this->option('check')) {

            $this->info('UUID '.count($uuid_classes));
            $this->info('TYPES '.count($type_name_uuids));
            $this->info('ATTRIBUTES '.count($attribute_name_uuids));
            $this->info('All '.count($what));

        }

        if ($this->option('list')) {

        }
        /*
         * todo need tasks
         *   :build
         *   :list
         *   :list_new
         *   :list_old
         */

        return 0;
    }
}
