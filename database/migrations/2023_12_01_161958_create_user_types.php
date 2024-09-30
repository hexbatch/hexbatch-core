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

            $table->foreignId('user_server_id')
                ->nullable()
                ->default(null)
                ->comment("The server this user belongs to, null means this one")
                ->index('idx_user_server_id')
                ->constrained('servers')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('user_type_id')
                ->nullable()->default(null)
                ->comment("The type made for this user. This is derived from at least one server type")
                ->unique('udx_user_type_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            //get the public and private types from the elements
            $table->foreignId('public_element_id')
                ->nullable()->default(null)
                ->comment("The element having the public information for the user")
                ->unique('udx_user_public_element_id')
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('private_element_id')
                ->nullable()->default(null)
                ->comment("The element having the private information for the user")
                ->unique('udx_private_element_id')
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('base_user_attribute_id')
                ->nullable()->default(null)
                ->comment("This is the attribute that is the parent for all attributes the user makes, which do not have a parent when created")
                ->unique('udx_base_user_attribute_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->nullOnDelete();



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



            $table->timestamps();

            $table->string('namespace',61)
                ->nullable(false)
                ->unique()
                ->comment("All things created by this user_token is prefixed by this name");
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
