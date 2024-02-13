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
        Schema::table('attribute_value_pointers', function (Blueprint $table) {
            $table->foreignId('remote_id')
                ->nullable()
                ->default(null)
                ->comment("The value points to a remote")
                ->index('idx_value_pointer_remote_id')
                ->constrained('remotes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('action_id')
                ->nullable()
                ->default(null)
                ->comment("The value points to an action")
                ->index('idx_value_pointer_action_id')
                ->constrained('actions')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('action_event_id')
                ->nullable()->default(null)
                ->comment("When the remote type is action_event")
                ->index('idx_value_action_event_id')
                ->constrained('action_events')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('search_path_id')
                ->nullable()->default(null)
                ->comment("When the remote type is action_event")
                ->index('idx_value_search_path_id')
                ->constrained('search_paths')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });

        DB::statement("ALTER TABLE attribute_value_pointers DROP CONSTRAINT chk_only_one_is_not_null;");

        DB::statement("ALTER TABLE attribute_value_pointers ADD CONSTRAINT chk_only_one_is_not_null CHECK (
            num_nonnulls(
                            location_bound_id, time_bound_id, element_id,element_type_id,attribute_id,
                            user_group_id,user_id,remote_id,action_id,action_event_id,search_path_id
            ) = 1)
        ;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        DB::statement("ALTER TABLE attribute_value_pointers DROP CONSTRAINT chk_only_one_is_not_null;");

        DB::statement("ALTER TABLE attribute_value_pointers ADD CONSTRAINT chk_only_one_is_not_null CHECK (
            num_nonnulls(location_bound_id, time_bound_id, element_id,element_type_id,attribute_id,user_group_id,user_id) = 1)
        ;");

        Schema::table('attribute_value_pointers', function (Blueprint $table) {

            $table->dropForeign(['remote_id']);
            $table->dropColumn('remote_id');
            $table->dropColumn('action_id');
            $table->dropColumn('action_event_id');
            $table->dropColumn('search_path_id');

        });
    }
};
