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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('element_type_id')
                ->after('id')
                ->nullable()
                ->default(null)
                ->comment("The user type")
                ->unique('udx_user_element_type_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->nullOnDelete();


            $table->foreignId('element_id')
                ->after('element_type_id')
                ->nullable()
                ->default(null)
                ->comment("The user element that stores the user data")
                ->unique('udx_user_element_id')
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->nullOnDelete();


            $table->foreignId('user_group_id')
                ->after('element_id')
                ->nullable()
                ->default(null)
                ->comment("The dedicated group for this user")
                ->unique('udx_user_dedicated_group_id')
                ->constrained('user_groups')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable()
                ->comment("used for display and id outside the code");

        });

        DB::statement('ALTER TABLE users ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');


        DB::statement("ALTER TABLE users ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON users FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['element_id']);
            $table->dropForeign(['element_type_id']);
            $table->dropForeign(['user_group_id']);
            $table->dropColumn('element_id','element_type_id','user_group_id');
            $table->dropColumn('ref_uuid');
        });

        DB::statement("DROP TRIGGER update_modified_time ON users");
    }
};
