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

            $table->foreignId('element_set_member_id')
                ->nullable(false)
                ->comment("The element/set these values are about")
                ->index()
                ->constrained('element_set_members')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();




            $table->foreignId('element_horde_id')
                ->nullable(false)
                ->comment("The attribute this value is about")
                ->index()
                ->constrained('element_type_hordes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('type_set_visibility_id')
                ->nullable()->default(null)
                ->comment("About if the type the attribute belongs to is visible or not in this set")
                ->index()
                ->constrained('element_type_set_visibilities')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();




            $table->foreignId('pointer_to_set_id')
                ->nullable()->default(null)
                ->comment("values can point to sets")
                ->index()
                ->constrained('element_sets')
                ->cascadeOnUpdate()
                ->restrictOnDelete();


            $table->foreignId('parent_element_value_id')
                ->nullable()->default(null)
                ->comment("when in push pop context, this is where the last value is")
                ->index()
                ->constrained('element_values')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();

            $table->boolean('is_on')->default(true)->nullable(false)
                ->comment('if off, then not seen by any rules. This can be local to the set');


            $table->timestamp('value_changed_at')->default(null)->nullable()
                ->comment('Updated when the value is updated here, otherwise null');

            $table->jsonb('element_value')
                ->nullable()->default(null)->comment("The value of the attribute in this row");

            $table->jsonb('element_shape_appearance')
                ->nullable()->default(null)->comment("How the shape is changed here per set");

        });

        DB::statement("ALTER TABLE element_values
                              Add COLUMN element_shape
                              geometry
                              ;
                    ");

        DB::statement("CREATE TYPE type_of_set_pointer_mode AS ENUM (
                'link_to_set'
            );");

        DB::statement("ALTER TABLE element_values Add COLUMN set_pointer_mode type_of_set_pointer_mode NOT NULL default 'link_to_set';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_values');
        DB::statement("DROP TYPE type_of_set_pointer_mode;");

    }
};
