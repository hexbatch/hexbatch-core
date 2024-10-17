<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
//todo drop table we filter via live types
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attribute_shape_event_filters', function (Blueprint $table) {
            $table->id();


            $table->foreignId('owner_shape_intersection_id')
                ->nullable(false)
                ->comment("The intersection that is filtered")
                ->index()
                ->constrained('attribute_shape_intersections')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('filtering_logic_attribute_id')
                ->nullable()->default(null)
                ->comment("Attribute whose intersection with the others determines what A and B are in the logic")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('filtered_event_id')
                ->nullable()
                ->comment("The set scoped event that will be filtered")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->integer('non_blocking_priority')->default(null)->nullable()
                ->comment('if null, the event will not be passed at all to the filtered attribute.if used, this is not a block, but sets priority on how the rules are organized in the thing tree.');
//

            $table->timestamps();

            $table->unique(['filtering_logic_attribute_id','filtered_event_id']);

        });


        DB::statement("ALTER TABLE attribute_shape_event_filters Add COLUMN blocking_logic type_of_logic NOT NULL default 'and';");
        DB::statement("ALTER TABLE attribute_shape_event_filters ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON attribute_shape_event_filters FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('attribute_shape_event_filters');
    }
};
