<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('remote_from_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_remote_id')
                ->nullable(false)
                ->comment("The remote uri this map is for")
                ->index('idx_remote_from_map_has_remote_uri_id')
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


            $table->string('remote_regex_match')->default(null)->nullable()
                ->comment('if the data in the remote is string, this is how we find that, multiple matches makes an array of strings');


            $table->string('remote_data_name')->default(null)->nullable()
                ->comment('the name of the data extracted');
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
