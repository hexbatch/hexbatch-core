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
        Schema::create('user_types', function (Blueprint $table) {
            $table->id();


            $table->foreignId('owner_user_id')
                ->nullable()
                ->default(null)
                ->comment("The owner of this type")
                ->index('idx_element_owner_user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('user_type_id')
                ->nullable()->default(null)
                ->comment("The type made for this user. This is derived from at least one server type")
                ->index('idx_user_type_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            //todo add two more types that are created when the user is: private info and public info, these are also put into the user home set

            //todo when the user home set is created from the user type element, its put into the Standard set, all_users

            //todo put in user_base_attribute for easy lookup

            $table->foreignId('user_home_set_id')
                ->nullable()
                ->default(null)
                ->comment("The user element that stores the user data")
                ->unique('udx_user_element_id')
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->restrictOnDelete();


            $table->foreignId('user_admin_group_id')
                ->nullable()
                ->default(null)
                ->comment("The dedicated group for this user")
                ->unique('udx_user_admin_group_id')
                ->constrained('user_groups')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('user_server_id')
                ->nullable()
                ->default(null)
                ->comment("The server this user belongs to, null means this one")
                ->index('idx_user_server_id')
                ->constrained('servers')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();
        });

        DB::statement("ALTER TABLE user_types ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON user_types FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_types');
    }
};
