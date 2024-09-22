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
        Schema::create('attribute_rule_bundles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('creator_attribute_id')
                ->nullable()
                ->default(null)
                ->comment("When a bundle is started, the original attribute. This is used later to see if ancestors can use this")
                ->index('idx_creator_parent_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();
        });


        DB::statement("ALTER TABLE attribute_rule_bundles ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON attribute_rule_bundles FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_rule_bundles');
    }
};
