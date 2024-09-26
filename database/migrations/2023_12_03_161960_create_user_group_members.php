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
        Schema::create('user_group_members', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_group_id')
                ->nullable(false)
                ->comment("The group this entry is for")
                ->index('idx_user_group_id')
                ->constrained('user_groups')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('member_user_type_id')
                ->nullable(false)
                ->comment("The group member/maybe admin too")
                ->index('idx_member_user_type_id')
                ->constrained('user_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->boolean('is_admin')->default(false)->nullable(false)
                ->comment('if true then member is admin');

            $table->timestamps();

            $table->unique(['user_group_id','member_user_type_id']);
        });


        DB::statement("ALTER TABLE user_group_members ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON user_group_members FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_group_members');
    }
};
