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
        Schema::create('element_values', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parent_element_id')
                ->nullable()->default(null)
                ->comment("The element these values are about")
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('element_horde_id')
                ->nullable()->default(null)
                ->comment("The attribute this value is about")
                ->constrained('element_type_hordes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('containing_set_id')
                ->nullable()->default(null)
                ->comment("The set this value is for")
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('pointer_to_set_id')
                ->nullable()->default(null)
                ->comment("values can point to sets")
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('parent_element_value_id')
                ->nullable()->default(null)
                ->comment("when in push pop context, this is where the last value is")
                ->constrained('element_values')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();


            $table->boolean('is_on')->default(true)->nullable(false)
                ->comment('if off, then not seen by any rules');

            $table->boolean('is_const')->default(true)->nullable(false)
                ->comment('if true, then get value from attribute via hord');

            $table->timestamp('dynamic_value_changed_at')->default(null)->nullable()
                ->comment('Updated when the value is updated here, otherwise null');

            $table->jsonb('element_value')
                ->nullable()->default(null)->comment("The value of the attribute in this row");

        });

        DB::statement("ALTER TABLE element_values
                              Add COLUMN element_shape
                              geometry
                              ;
                    ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_values');
    }
};
