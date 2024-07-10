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

        Schema::create('attribute_value_pointers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('value_lookup_attribute_id')
                ->nullable(false)
                ->comment("The attribute this value is for")
                ->unique('udx_parent_attribute_value_id')
                ->constrained('attribute_values')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();



            $table->foreignId('user_id')
                ->nullable()
                ->default(null)
                ->comment("The value points to a user")
                ->index('idx_value_pointer_user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('user_group_id')
                ->nullable()
                ->default(null)
                ->comment("The value points to a user group")
                ->index('idx_value_pointer_group_id')
                ->constrained('user_groups')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('attribute_id')
                ->nullable()
                ->default(null)
                ->comment("The value points to an attribute")
                ->index('idx_value_pointer_attribute_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('element_type_id')
                ->nullable()
                ->default(null)
                ->comment("The value points to a type")
                ->index('idx_value_pointer_type_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('element_id')
                ->nullable()
                ->default(null)
                ->comment("The value points to an element")
                ->index('idx_value_pointer_element_id')
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('time_bound_id')
                ->nullable()
                ->default(null)
                ->comment("The value points to a time bounds")
                ->index('idx_value_pointer_time_id')
                ->constrained('time_bounds')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('location_bound_id')
                ->nullable()
                ->default(null)
                ->comment("The value points to a location bounds")
                ->index('idx_value_pointer_location_id')
                ->constrained('location_bounds')
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

            $table->foreignId('set_id')
                ->nullable()
                ->default(null)
                ->comment("The value points to set ")
                ->index('idx_value_pointer_set_id')
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('path_id')
                ->nullable()
                ->default(null)
                ->comment("The value points to a path ")
                ->index('idx_value_pointer_path_id')
                ->constrained('paths')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('remote_id')
                ->nullable()
                ->default(null)
                ->comment("The value points to a remote")
                ->index('idx_value_pointer_remote_id')
                ->constrained('remotes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();
        });



        DB::statement("ALTER TABLE attribute_value_pointers ALTER COLUMN created_at SET DEFAULT NOW();");
        DB::statement("ALTER TABLE attribute_value_pointers ADD CONSTRAINT chk_only_one_is_not_null CHECK (
            num_nonnulls(location_bound_id, time_bound_id, element_id,element_type_id,
                            attribute_id,user_group_id,user_id,action_id,set_id,path_id,remote_id) = 1)
        ;");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON attribute_value_pointers FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_value_pointers');
    }
};
