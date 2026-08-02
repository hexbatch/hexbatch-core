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


        Schema::create('live_permissions', function (Blueprint $table) {

            $table->comment("if rules and triggers are same group, or the rules are admin for the trigger groups, no entry is needed here");

            $table->id();

            $table->foreignId('live_permission_giver_ns_id')
                ->nullable(false)
                ->comment("The namespace that gave this permission")
                ->index()
                ->constrained('user_namespaces')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('live_permission_trigger_type_id')
                ->nullable(false)
                ->comment("The base type that can be lived. If a type has this as a base type, this applies to the derived type also")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('live_permission_target_type_id')
                ->nullable(false)
                ->comment("The base type that the live can be. If live has this as a base or ancestor, the entire type will be used")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->boolean('can_add_listeners')->default(false)->nullable(false)
                ->index()
                ->comment('if true, then action listeners from the live  are added to the element. Otherwise any attributes added will not use their action listeners');


            $table->boolean('can_add_bounds')->default(false)->nullable(false)
                ->index()
                ->comment('if true, then bound changes from live attributes are added to the element. Otherwise element will not have bounds adjusted');



            $table->timestamp('created_at')
                ->default(DB::raw('NOW()'))
                ->comment("When created");

            $table->timestamp('updated_at')
                ->default(null)
                ->nullable()
                ->comment("When updated");

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");


            $table->unique(['live_permission_trigger_type_id','live_permission_target_type_id']);

        });

        DB::statement('ALTER TABLE live_permissions ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON live_permissions FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_permissions');
    }
};
