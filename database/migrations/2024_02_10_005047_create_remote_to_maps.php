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
            $table->foreignId('remote_id')
                ->nullable(false)
                ->comment("The remote this header/key is for")
                ->index('idx_header_setting_has_remote_id')
                ->constrained('remotes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->boolean('is_secret')->nullable(false)->default(false)->comment("If secret then not showing up in results");

            $table->timestamps();


        });

        DB::statement("CREATE TYPE type_of_remote_output_map AS ENUM (
            'none','basic_auth','bearer_auth','data','header'
            );");

        DB::statement("ALTER TABLE remote_to_maps Add COLUMN map_type type_of_remote_output_map NOT NULL default 'none';");

        DB::statement("ALTER TABLE remote_to_maps ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON remote_to_maps FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        Schema::table('remote_to_maps', function (Blueprint $table) {
            $table->string('holder_json_path')->default(null)->nullable()
                ->comment('how to get the data in the holder, if var value is null, then this will be used instead');

            $table->string('header_var_name')->nullable(false)->comment("The name of the header or setting");
            $table->text('header_var_value')->nullable(true)->default(null)->comment("The value of the header or setting");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remote_to_maps');
        DB::statement("DROP TYPE type_of_remote_output_map;");
    }
};
