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
            "../migration_triggers_and_procs/2023_12_03_231960_create_user_group_member_triggers/1_member_recalc_active_group_members.sql");

        \App\Helpers\Utilities::runDbFile(
            "../migration_triggers_and_procs/2023_12_03_231960_create_user_group_member_triggers/2_member_cleanup_active_group_members.sql");

        DB::statement("
            CREATE TRIGGER set_user_group_member_mode_ins AFTER INSERT ".
            "ON user_group_members FOR EACH ROW EXECUTE PROCEDURE members_recalc_active_group_members();
        ");

        DB::statement("
            CREATE TRIGGER set_user_group_member_mode_ups AFTER UPDATE ".
            "ON user_group_members FOR EACH ROW EXECUTE PROCEDURE members_recalc_active_group_members();
        ");

        DB::statement("
            CREATE TRIGGER cleanup_active_members_del BEFORE DELETE ".
            "ON user_group_members FOR EACH ROW EXECUTE PROCEDURE member_cleanup_active_group_members();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TRIGGER set_user_group_member_mode_ups ON user_group_members");
        DB::statement("DROP TRIGGER set_user_group_member_mode_ins ON user_group_members");
        DB::statement("DROP TRIGGER cleanup_active_members_del ON user_group_members");

        DB::statement("
            DROP FUNCTION IF EXISTS members_recalc_active_group_members();
        ");

        DB::statement("
            DROP FUNCTION IF EXISTS member_cleanup_active_group_members();
        ");
    }
};
