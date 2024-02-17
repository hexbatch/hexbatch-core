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
        Schema::create('remote_from_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('remote_id')
                ->nullable(false)
                ->comment("The remote this map is for")
                ->index('idx_header_map_has_remote_id')
                ->constrained('remotes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();





            $table->timestamps();

        });

        DB::statement("CREATE TYPE type_from_remote_map AS ENUM (
            'none','data','header','response_code'
            );");

        DB::statement("ALTER TABLE remote_from_maps Add COLUMN map_type type_from_remote_map NOT NULL default 'none';");

        DB::statement("ALTER TABLE remote_from_maps ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON remote_from_maps FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        Schema::table('remote_from_maps', function (Blueprint $table) {

            $table->string('remote_json_path')->default(null)->nullable()
                ->comment('If the data in the remote is json (post or json). This is how we get and set that');

            $table->string('remote_xpath')->default(null)->nullable()
                ->comment('If the data in the remote is xml. This is how we get that');

            $table->string('remote_regex_split')->default(null)->nullable()
                ->comment('if the data in the remote is string, this is how we break apart what we find in the match, empty for no splitting');

            $table->string('remote_regex_match')->default(null)->nullable()
                ->comment('if the data in the remote is string, this is how we find that');


            $table->string('holder_json_path')->default(null)->nullable()
                ->comment('how to put the data in the holder');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remote_from_maps');
        DB::statement("DROP TYPE type_from_remote_map");
    }
};
