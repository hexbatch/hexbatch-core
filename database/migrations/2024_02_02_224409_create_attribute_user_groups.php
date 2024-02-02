<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
parent_attribute_id
    user_group_id
    group_type: usage|read|write
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attribute_user_groups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('group_parent_attribute_id')
                ->nullable(false)
                ->comment("The attribute this rule is for")
                ->index('idx_user_group_attribute_parent_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('target_user_group_id')
                ->nullable(false)
                ->comment("The user group this is aboutr")
                ->unique('udx_attribute_user_group_id')
                ->constrained('user_groups')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->timestamps();
        });

        DB::statement("CREATE TYPE type_of_attribute_user_group AS ENUM (
            'inactive',
            'read','write','usage'
            );");

        DB::statement("ALTER TABLE attribute_user_groups Add COLUMN group__type type_of_attribute_user_group NOT NULL default 'inactive';");

        DB::statement("ALTER TABLE attribute_user_groups ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON attribute_user_groups FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_user_groups');
        DB::statement("DROP TYPE type_of_attribute_user_group;");
    }
};
