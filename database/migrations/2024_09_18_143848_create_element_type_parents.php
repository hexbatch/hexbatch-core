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
        Schema::create('element_type_parents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('child_type_id')
                ->nullable()->default(null)
                ->comment("The child who is inherting the parent")
                ->index('idx_element_type_child_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('parent_type_id')
                ->nullable()->default(null)
                ->comment("The parent")
                ->index('idx_element_type_parent_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->integer('parent_rank')->nullable(false)->default(1)
                ->comment("The order of the parent being inherited");

            $table->boolean('is_active')->default(false)->nullable(false)
                ->comment('if true then the parent attributes are active in the child elements, else not');

            $table->timestamps();
        });

        DB::statement("ALTER TABLE element_type_parents ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON element_type_parents FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_type_parents');
    }
};
