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
        Schema::create('mutual_members', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mutual_parent_id')
                ->nullable()->default(null)
                ->comment("the mutual this is about")
                ->index('idx_mutual_parent_id')
                ->constrained('mutuals')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('from_mutual_element_id')
                ->nullable()->default(null)
                ->comment("Each element is connected to another via sets they belong to, this is A where A has a relation to B")
                ->index('idx_from_mutual_element_id')
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('to_mutual_element_id')
                ->nullable()->default(null)
                ->comment("Each element is connected to another via sets they belong to, this is B where A has a relation to B")
                ->index('idx_to_mutual_element_id')
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('from_source_set_id')
                ->nullable()->default(null)
                ->comment("Optional set for live mutuals")
                ->index('idx_from_source_set_id')
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('to_source_set_id')
                ->nullable()->default(null)
                ->comment("Optional set for live mutuals")
                ->index('idx_to_source_set_id')
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();
        });

        DB::statement("ALTER TABLE mutual_members ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON mutual_members FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutual_members');
    }
};
