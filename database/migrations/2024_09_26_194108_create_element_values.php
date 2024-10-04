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
                ->index()
                ->constrained('elements')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('element_horde_id')
                ->nullable(false)
                ->comment("The attribute this value is about")
                ->index()
                ->constrained('element_type_hordes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            //todo mark somehow if this is from its design or live, so can lookup what to do (bool, enum?)

            $table->foreignId('containing_set_id')
                ->nullable()->default(null)
                ->comment("The set this value is for")
                ->index()
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('pointer_to_set_id')
                ->nullable()->default(null)
                ->comment("values can point to sets")
                ->index()
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            //todo put enum what sort of pointer : to a child set or to a link

            $table->foreignId('parent_element_value_id')
                ->nullable()->default(null)
                ->comment("when in push pop context, this is where the last value is")
                ->index()
                ->constrained('element_values')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();


            $table->boolean('is_on')->default(true)->nullable(false)
                ->comment('if off, then not seen by any rules');

            //todo drop is_const
            $table->boolean('is_const')->default(true)->nullable(false)
                ->comment('if true, then get value from attribute via hord');

            //todo rename
            $table->timestamp('dynamic_value_changed_at')->default(null)->nullable()
                ->comment('Updated when the value is updated here, otherwise null');

            $table->jsonb('element_value')
                ->nullable()->default(null)->comment("The value of the attribute in this row");

        });
        //todo add new column for json that affects how the shape looks (color:texture, opacity, ordering for those that are using same bound parts for which is drawn)

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
