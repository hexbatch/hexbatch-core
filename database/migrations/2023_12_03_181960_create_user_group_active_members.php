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
        //todo admin flag
        Schema::create('user_group_active_members', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_group_id')
                ->nullable(false)
                ->comment("The group this entry is for")
                ->index('idx_active_member_group_id')
                ->constrained('user_groups')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('user_id')
                ->nullable(false)
                ->comment("The group member/maybe admin too")
                ->index('idx_active_member_user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            //todo add child user group link too
            $table->foreignId('parent_user_group_id')
                ->nullable()
                ->default(null)
                ->comment("The user was supplied by this group")
                ->index('idx_active_member_parent_group_id')
                ->constrained('user_groups')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->unique(['user_group_id','user_id']);

        });
        DB::statement("ALTER TABLE user_group_active_members ADD CONSTRAINT chk_not_same_groups CHECK (
                parent_user_group_id is null OR  (parent_user_group_id <> user_group_id)
            );");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_group_active_members');
    }
};
