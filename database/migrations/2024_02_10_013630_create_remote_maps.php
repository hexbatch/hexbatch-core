<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * remote_json_path remote_xpath attribute_json_path attribute_id action_id
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('remote_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('remote_id')
                ->nullable(false)
                ->comment("The remote this map is for")
                ->index('idx_header_map_has_remote_id')
                ->constrained('remotes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('map_attribute_id')
                ->nullable(true)->default(null)
                ->comment("The attribute this map is about")
                ->index('idx_map_attribute_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();





            $table->timestamps();

        });

        DB::statement("CREATE TYPE type_of_header_map AS ENUM (
            'none','input_attribute','output_attribute'
            );");

        DB::statement("ALTER TABLE remote_maps Add COLUMN map_type type_of_header_map NOT NULL default 'none';");

        DB::statement("ALTER TABLE remote_maps ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON remote_maps FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        Schema::table('remote_maps', function (Blueprint $table) {

            $table->string('remote_json_path')->default(null)->nullable()
                ->comment('how to get the json data from the remote response');

            $table->string('remote_xpath')->default(null)->nullable()
                ->comment('how to get the xml data from the remote response');

            $table->string('remote_header_regex')->default(null)->nullable()
                ->comment('how to get the header data from the remote response');

            $table->string('attribute_json_path')->default(null)->nullable()
                ->comment('how to get or set the json data from/to the attribute. null means all the data');

            $table->string('key_path')->default(null)->nullable()
                ->comment('this data is mapped via xml, json,header to a json response under this key structure,'.
                    ' which can become an array if multiple use this. null for all replacement to attribute or top level data to server');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remote_maps');
        DB::statement("DROP TYPE type_of_header_map");
    }
};
