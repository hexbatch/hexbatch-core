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
        Schema::create('remote_to_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_remote_id')
                ->nullable(false)
                ->comment("The remote this header/key is for")
                ->index('idx_remote_to_maps_has_remote_id')
                ->constrained('remotes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->boolean('is_secret')->nullable(false)->default(false)->comment("If secret then not showing up in results");

            $table->timestamps();


        });

        DB::statement("CREATE TYPE type_to_remote_map AS ENUM (
            'none','data','header','file'
            );");

        DB::statement("ALTER TABLE remote_to_maps Add COLUMN map_type type_to_remote_map NOT NULL default 'none';");

        # -------------------------------


        DB::statement("ALTER TABLE remote_to_maps Add COLUMN cast_data_to_format type_remote_data_format NULL default NULL;");

        #---------------------------------------------------------------

        DB::statement("ALTER TABLE remote_to_maps ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON remote_to_maps FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        Schema::table('remote_to_maps', function (Blueprint $table) {

            $table->string('holder_json_path')->default(null)->nullable()
                ->comment('how to get the data in the holder, if remote_data_constant value is null, then this will be used instead');

            $table->string('remote_data_name')->nullable(false)->comment("The name of the header, setting, json key or xml tag");
            $table->jsonb('remote_data_constant')->nullable()->default(null)->comment("The value of the header or setting");

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remote_to_maps');
        DB::statement("DROP TYPE type_to_remote_map;");
    }
};
