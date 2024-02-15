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
            "../migration_triggers_and_procs/2023_12_03_231960_create_user_group_member_triggers/1_group_recalc_active_group_members.sql");

        \App\Helpers\Utilities::runDbFile(
            "../migration_triggers_and_procs/2023_12_03_231950_create_user_group_triggers/2_user_group_stop_parent_recursion.sql");

        \App\Helpers\Utilities::runDbFile(
            "../migration_triggers_and_procs/2023_12_03_231950_create_user_group_triggers/3_group_cleanup_active_group_members.sql");


        DB::statement("
            CREATE TRIGGER user_group_stop_recursion_ins BEFORE INSERT ".
            "ON user_groups FOR EACH ROW EXECUTE PROCEDURE user_group_stop_parent_recursion();
        ");

        DB::statement("
            CREATE TRIGGER user_group_stop_recursion_ups BEFORE UPDATE ".
            "ON user_groups FOR EACH ROW EXECUTE PROCEDURE user_group_stop_parent_recursion();
        ");

        DB::statement("
            CREATE TRIGGER user_group_recalc_active_ins AFTER INSERT ".
            "ON user_groups FOR EACH ROW EXECUTE PROCEDURE group_recalc_active_group_members();
        ");

        DB::statement("
            CREATE TRIGGER user_group_recalc_active_ups AFTER UPDATE ".
            "ON user_groups FOR EACH ROW EXECUTE PROCEDURE group_recalc_active_group_members();
        ");

        DB::statement("
            CREATE TRIGGER user_group_undo_active_del BEFORE DELETE ".
            "ON user_groups FOR EACH ROW EXECUTE PROCEDURE group_undo_active_group_members();
        ");




    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TRIGGER user_group_stop_recursion_ups ON user_group_members");
        DB::statement("DROP TRIGGER user_group_stop_recursion_ins ON user_group_members");

        DB::statement("DROP TRIGGER user_group_recalc_active_ups ON user_group_members");
        DB::statement("DROP TRIGGER user_group_recalc_active_ins ON user_group_members");

        DB::statement("DROP TRIGGER user_group_undo_active_del ON user_group_members");


        DB::statement("
            DROP FUNCTION IF EXISTS user_group_stop_parent_recursion();
        ");

        DB::statement("
            DROP FUNCTION IF EXISTS group_recalc_active_group_members();
        ");

        DB::statement("
            DROP FUNCTION IF EXISTS group_undo_active_group_members();
        ");
    }
};
