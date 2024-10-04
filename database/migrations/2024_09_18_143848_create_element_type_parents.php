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

            $table->unique(['parent_type_id','child_type_id']);

            $table->integer('parent_rank')->nullable(false)->default(1)
                ->comment("The order of the parent being inherited");


            $table->timestamps();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");
            //todo enum column type_approval_status pending|approved|not_applicable

        });

        DB::statement('ALTER TABLE element_type_parents ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');

        DB::statement("ALTER TABLE element_type_parents ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON element_type_parents FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
        //todo add parent_type enum : design (default) | live
        //todo add merge strategy when combining same attributes
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_type_parents');
    }
};
