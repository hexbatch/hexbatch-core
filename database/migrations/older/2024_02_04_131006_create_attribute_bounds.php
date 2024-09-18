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

        Schema::create('attribute_bounds', function (Blueprint $table) {
            $table->id();


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


            $table->boolean('is_read_policy_all')->default(false)->nullable(false)
                ->comment('if true then all the attibutes listed in the  need to be in the set to read this in a set context');

            $table->boolean('is_write_policy_all')->default(false)->nullable(false)
                ->comment('if true then all the attibutes need to be in the set to write this in a set context');





            $table->timestamps();

        });


        DB::statement("ALTER TABLE attribute_bounds ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON attribute_bounds FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    } //up

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_bounds');
    }
};
