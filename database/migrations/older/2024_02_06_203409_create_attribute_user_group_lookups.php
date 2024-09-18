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
        Schema::create('attribute_user_group_lookups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('group_lookup_attribute_id')
                ->nullable(false)
                ->comment("The attribute this group is for")
                ->index('idx_group_lookup_attribute_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('group_managed_by_attribute_id')
                ->nullable(false)
                ->comment("The attribute that manages this. ".
                    "Set to the ancestor|parent|child based on who set this in the inheritance chain. "
                )
                ->index('idx_group_managed_by_attribute_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('target_user_group_id')
                ->nullable(false)
                ->comment("The user group this is about")
                ->index('idx_target_user_group_id')
                ->constrained('user_groups')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->timestamps();
        });

        DB::statement("CREATE TYPE type_of_attribute_user_group AS ENUM (
            'inactive',
            'read','write','usage'
            );");

        DB::statement("ALTER TABLE attribute_user_group_lookups Add COLUMN group_type type_of_attribute_user_group NOT NULL default 'inactive';");


        DB::statement("ALTER TABLE attribute_user_group_lookups ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON attribute_user_group_lookups FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_user_group_lookups');
        DB::statement("DROP TYPE type_of_attribute_user_group;");
    }
};
