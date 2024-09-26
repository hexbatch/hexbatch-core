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
        Schema::create('element_set_contents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('owning_set_id')
                ->nullable()->default(null)
                ->comment("the set this is about")
                ->index('idx_owning_set_id')
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('set_content_element_id')
                ->nullable()->default(null)
                ->comment("The element that belongs to the set ")
                ->index('idx_set_content_element_id')
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();
        });



        DB::statement("ALTER TABLE element_sets ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON element_set_contents FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_set_contents');
    }
};
