<?php

namespace App\Console\Commands;

use App\Helpers\Attributes\Apply\StandardAttributes;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateStandardResources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hbc:create_standards';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates any missing standard resources';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line("Existing resources");
        $this->line("-------------------");
        foreach (StandardAttributes::getAttributeCache() as $thing) {
            $whjat = $thing->getName();
            $this->info($whjat);
        }
       // return;
        try {
            DB::beginTransaction();
            $b_new = false;
            $system_user = User::getOrCreateSystemUser($b_new);
            if ($b_new) {
                $this->info("created system user " . $system_user->getName());
            }

            $created = StandardAttributes::generateMissingAttributes();
            $names = [];
            if (count($created)) {

                foreach ($created as $c) {
                    $names[] = [$c->attribute_name];
                }
            }

            if (count($names)) {

                $this->table(['attribute name'], $names);
            } else {
                $this->info("No attributes made");
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage());
        }

    }
}
