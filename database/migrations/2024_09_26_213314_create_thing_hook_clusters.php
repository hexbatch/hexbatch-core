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
        Schema::create('thing_hook_clusters', function (Blueprint $table) {
            $table->id();


            $table->boolean('is_on')->default(true)->nullable(false)
                ->comment('if false then this cluster is not applied to new things');

            $table->foreignId('hook_on_action_id')
                ->nullable()->default(null)
                ->comment("filter for one action")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('hook_on_api_id')
                ->nullable()->default(null)
                ->comment("filter for one api call")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('hook_on_base_rule_type_id')
                ->nullable()->default(null)
                ->comment("filter for one or more families of types whose rules call this")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('hook_on_base_set_type_id')
                ->nullable()->default(null)
                ->comment("Filter if thing made from an element this set family")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('hook_on_member_namespace_id')
                ->nullable()
                ->default(null)
                ->comment("Filter if caller ns is a member in this ns")
                ->index()
                ->constrained('user_namespaces')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('hook_on_admin_namespace_id')
                ->nullable()
                ->default(null)
                ->comment("Filter if caller ns is an admin in this ns")
                ->index()
                ->constrained('user_namespaces')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->string('thing_cluster_name')->nullable()->default(null)
                ->comment('optional name');

            $table->text('thing_cluster_notes')->nullable()->default(null)
                ->comment('optional notes');
        });

        DB::statement("ALTER TABLE thing_hook_clusters ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON thing_hook_clusters FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        DB::statement('ALTER TABLE thing_hook_clusters ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thing_hook_clusters');
    }
};
