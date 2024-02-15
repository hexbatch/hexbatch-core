<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        $path = realpath(__DIR__ .
            "../migration_triggers_and_procs/2023_12_03_231960_create_user_group_member_triggers/1_recalc_group_member_procs.sql");
        if (!$path) {
            throw new LogicException("could not find file in migration: 1_recalc_group_member_procs.sql");
        }
        $proc = file_get_contents($path);
        if (!$proc) {
            throw new LogicException("could not read file in migration: 1_recalc_group_member_procs.sql");
        }
        DB::statement($proc);

        DB::statement("
            CREATE TRIGGER set_user_group_member_mode_ins BEFORE INSERT ".
            "ON user_group_members FOR EACH ROW EXECUTE PROCEDURE recalc_user_group_membership_type();
        ");

        DB::statement("
            CREATE TRIGGER set_user_group_member_mode_ups BEFORE UPDATE ".
            "ON user_group_members FOR EACH ROW EXECUTE PROCEDURE recalc_user_group_membership_type();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TRIGGER set_user_group_member_mode_ups ON user_group_members");
        DB::statement("DROP TRIGGER set_user_group_member_mode_ins ON user_group_members");

        DB::statement("
            DROP FUNCTION IF EXISTS recalc_user_group_membership_type();
        ");
    }
};
