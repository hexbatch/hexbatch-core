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

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->foreignId('element_id')
                ->nullable(false)
                ->comment("The user element that stores the user data")
                ->index('idx_group_element_id')
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('user_id')
                ->nullable(false)
                ->comment("The owner")
                ->index('idx_group_member_user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->boolean('is_admin')->default(false)->nullable(false)
                ->comment('if true then member is admin');

            $table->timestamps();

            $table->unique('element_id','user_id');
        });

        DB::statement('ALTER TABLE user_groups ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');


        DB::statement("ALTER TABLE user_groups ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON user_groups FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_groups');
    }
};
