<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \App\Helpers\Utilities::runDbFile(
            "../migration_triggers_and_procs/2023_12_03_231950_create_user_group_triggers/1_user_group_stop_parent_recursion.sql");


        DB::statement("
            CREATE TRIGGER user_group_stop_recursion_ins BEFORE INSERT ".
            "ON user_groups FOR EACH ROW EXECUTE PROCEDURE user_group_stop_parent_recursion();
        ");

        DB::statement("
            CREATE TRIGGER user_group_stop_recursion_ups BEFORE UPDATE ".
            "ON user_groups FOR EACH ROW EXECUTE PROCEDURE user_group_stop_parent_recursion();
        ");

        \App\Helpers\Utilities::runDbFile(
            "../migration_triggers_and_procs/2023_12_03_231950_create_user_group_triggers/2_user_group_nudge_owner_member_when.sql");

        DB::statement("
            CREATE TRIGGER user_group_nudge_owner_member_when_ins BEFORE INSERT ".
            "ON user_groups FOR EACH ROW EXECUTE PROCEDURE user_group_nudge_owner_member_when();
        ");

        DB::statement("
            CREATE TRIGGER user_group_nudge_owner_member_when_ups BEFORE UPDATE ".
            "ON user_groups FOR EACH ROW EXECUTE PROCEDURE user_group_nudge_owner_member_when();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TRIGGER user_group_stop_recursion_ups ON user_group_members");
        DB::statement("DROP TRIGGER user_group_stop_recursion_ins ON user_group_members");
        DB::statement("DROP TRIGGER user_group_nudge_owner_member_when_ins ON user_group_members");
        DB::statement("DROP TRIGGER user_group_nudge_owner_member_when_ups ON user_group_members");

        DB::statement("
            DROP FUNCTION IF EXISTS user_group_stop_parent_recursion();
        ");

        DB::statement("
            DROP FUNCTION IF EXISTS user_group_nudge_owner_member_when();
        ");
    }
};
