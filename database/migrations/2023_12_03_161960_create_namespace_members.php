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
        Schema::create('user_namespace_members', function (Blueprint $table) {

            $table->id();

            $table->foreignId('parent_namespace_id')
                ->nullable(false)
                ->comment("The namespace this entry is for")
                ->index('idx_user_namespace_id')
                ->constrained('user_namespaces')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('member_namespace_id')
                ->nullable(false)
                ->comment("The member namespace")
                ->index('idx_member_namespace_id')
                ->constrained('user_namespaces')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->boolean('is_admin')->default(false)->nullable(false)
                ->comment('if true then member is admin');

            $table->timestamps();

            $table->unique(['parent_namespace_id','member_namespace_id']);
        });


        DB::statement("ALTER TABLE user_namespace_members ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON user_namespace_members FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_namespace_members');
    }
};
