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
        Schema::create('user_groups', function (Blueprint $table) {

            $table->id();





            $table->foreignId('user_id')
                ->nullable(false)
                ->comment("The owner")
                ->index('idx_group_owner_user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('parent_user_group_id')
                ->nullable()
                ->default(null)
                ->comment("Optional parent group")
                ->unique('udx_user_group_has_parent_id')
                ->constrained('user_groups')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");


            $table->timestamps();

        });

        #--------------------------------------
        DB::statement("CREATE TYPE type_user_group_parent_combination AS ENUM (
            'none','parent_union','parent_intersection'
            );");

        DB::statement("ALTER TABLE user_groups Add COLUMN parent_combine_strategy type_user_group_parent_combination NOT NULL default 'none';");
        #--------------------------------------


        DB::statement('ALTER TABLE user_groups ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');


        DB::statement("ALTER TABLE user_groups ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON user_groups FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        Schema::table('user_groups', function (Blueprint $table) {

            $table->string('group_name',128)
                ->nullable(false)
                ->comment("The unique name of the group, using the naming rules");


            $table->unique(['user_id','group_name']);
        });


        //add user group to users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('user_group_id')
                ->after('element_id')
                ->nullable()
                ->default(null)
                ->comment("The dedicated group for this user")
                ->unique('udx_user_dedicated_group_id')
                ->constrained('user_groups')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['user_group_id']);
            $table->dropColumn('user_group_id');
        });


        Schema::dropIfExists('user_groups');

        DB::statement("DROP TYPE type_user_group_parent_combination");
    }
};
