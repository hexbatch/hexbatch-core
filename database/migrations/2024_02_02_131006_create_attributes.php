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

        Schema::create('attributes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->default(null)
                ->comment("The owner of the attribute")
                ->index('idx_attribute_user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('parent_attribute_id')
                ->nullable()
                ->default(null)
                ->comment("The optional parent of the attribute")
                ->index('idx_attribute_parent_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('read_time_bounds_id')
                ->nullable()
                ->default(null)
                ->comment("reading this follows an optional schedule")
                ->index('idx_attribute_read_time_bounds_id')
                ->constrained('time_bounds')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('write_time_bounds_id')
                ->nullable()
                ->default(null)
                ->comment("writing this follows an optional schedule")
                ->index('idx_attribute_write_time_bounds_id')
                ->constrained('time_bounds')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('read_map_location_bounds_id')
                ->nullable()
                ->default(null)
                ->comment("Reading this depends on an optional map location")
                ->index('idx_attribute_read_map_bounds_id')
                ->constrained('location_bounds')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('write_map_location_bounds_id')
                ->nullable()
                ->default(null)
                ->comment("Writing this depends on an optional map location")
                ->index('idx_attribute_write_map_bounds_id')
                ->constrained('location_bounds')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('read_shape_location_bounds_id')
                ->nullable()
                ->default(null)
                ->comment("Reading this depends on an optional position inside a shape")
                ->index('idx_attribute_read_shape_bounds_id')
                ->constrained('location_bounds')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('write_shape_location_bounds_id')
                ->nullable()
                ->default(null)
                ->comment("Writing this depends on an optional position inside a shape")
                ->index('idx_attribute_write_shape_bounds_id')
                ->constrained('location_bounds')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('read_path_bounds_id')
                ->nullable()
                ->default(null)
                ->comment("reading this depends on where the attribute is")
                ->index('idx_attribute_read_paths_id')
                ->constrained('paths')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('write_path_bounds_id')
                ->nullable()
                ->default(null)
                ->comment("writing this depends where the attribute is")
                ->index('idx_attribute_write_paths_id')
                ->constrained('paths')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->boolean('is_retired')->default(false)->nullable(false)
                ->comment('if true then cannot be added to types or new live-attribute on elements');


            $table->boolean('is_final')->default(false)->nullable(false)
                ->comment('if true then cannot be used as a parent');

            $table->boolean('is_system')->default(false)->nullable(false)
                ->index('idx_is_standard')
                ->comment('if true then this attribute is a standard attribute');


            $table->boolean('is_human')->default(false)->nullable(false)
                ->comment('if true then shows up in a human friendly filter');

            $table->boolean('is_read_policy_all')->default(false)->nullable(false)
                ->comment('if true then all the attibutes need to be in the set to read this in a set context');

            $table->boolean('is_write_policy_all')->default(false)->nullable(false)
                ->comment('if true then all the attibutes need to be in the set to write this in a set context');

            $table->boolean('is_nullable')->default(true)->nullable(false)
                ->comment('if true then value is nullable');



            $table->timestamps();
            $table->string('attribute_name',128)->nullable(false)->index()
                ->comment("The unique name of the attribute, using the naming rules");


        });

        DB::statement(/** @lang text */
            "CREATE UNIQUE INDEX udx_user_parent_name ON attributes (user_id,parent_attribute_id,attribute_name) NULLS NOT DISTINCT;");



        DB::statement('ALTER TABLE attributes ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');


        DB::statement("ALTER TABLE attributes ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON attributes FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    } //up

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};
