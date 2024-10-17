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
        Schema::create('element_set_children', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parent_set_id')
                ->nullable()->default(null)
                ->comment("the set this is about")
                ->index('idx_parent_set_id')
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('child_set_id')
                ->nullable()->default(null)
                ->comment("The element that belongs to the set ")
                ->index('idx_child_set_id')
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            //todo add new column for defining element call it handle..
            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->timestamps();
        });

        DB::statement('ALTER TABLE element_set_children ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');
        DB::statement("ALTER TABLE element_set_children ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON element_set_children FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_set_children');
    }
};
