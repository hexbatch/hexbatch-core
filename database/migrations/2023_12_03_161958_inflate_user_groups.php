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
        Schema::table('user_groups', function (Blueprint $table) {


            $table->foreignId('user_id')
                ->nullable(false)
                ->comment("The owner")
                ->index('idx_group_owner_user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");


            $table->timestamps();

        });



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



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('user_groups', function (Blueprint $table) {

            DB::statement("DROP TRIGGER update_modified_time ON user_groups");

            $table->dropForeign(['user_id']);
            $table->dropColumn('group_name');
            $table->dropColumn('ref_uuid');
            $table->dropColumn('user_id');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });


    }
};
