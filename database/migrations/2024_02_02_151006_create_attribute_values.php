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

        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();


            $table->foreignId('parent_attribute_id')
                ->nullable()
                ->default(null)
                ->comment("The optional parent of the attribute")
                ->index('idx_attribute_value_parent_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();



            $table->boolean('is_nullable')->default(true)->nullable(false)
                ->comment('if true then value is nullable');




        });

        DB::statement("CREATE TYPE type_of_attribute_value AS ENUM (
            'numeric','string','json',
            'user','user_group','attribute','element','element_type',
            'remote','action','search',
            'schedule_bounds','map_bounds','shape_bounds',
            'view','mutual','container',
            'coordinate_map','coordinate_shape'
            );");

        DB::statement("ALTER TABLE attribute_values Add COLUMN value_type type_of_attribute_value NOT NULL default 'string';");

        Schema::table('attribute_values', function (Blueprint $table) {


            $table->float('value_numeric_min')->nullable()->default(null)
                ->comment("if set and this value type is number, then this is the min allowed for the value");

            $table->float('value_numeric_max')->nullable()->default(null)
                ->comment("if set and this value type is number, then this is the max allowed for the value");


            $table->jsonb('json_value_default')->nullable()->default(null)->comment("set if json");
            $table->text('text_value_default')->nullable()->default(null)->comment("set if string");



            $table->string('value_regex')->nullable()->default(null)
                ->comment("if set and this is plain string, then regex filters this");

            $table->timestamps();
        });





        DB::statement("ALTER TABLE attribute_values ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON attribute_values FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    } //up

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_values');
        DB::statement("DROP TYPE type_of_attribute_value;");
    }
};
