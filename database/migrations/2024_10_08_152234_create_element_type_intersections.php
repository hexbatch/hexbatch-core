<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    // pop for live types

    public function up(): void
    {
        Schema::create('element_type_intersections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('type_intersection_set_member_id')
                ->nullable(false)
                ->comment("The element and the set this intersecting bounds is about")
                ->index()
                ->constrained('element_set_members')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('intersection_earlier_type_id')
                ->nullable(false)
                ->comment("The type that was already here in the set. Extra row for each type added live to the element")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('intersection_later_type_id')
                ->nullable(false)
                ->comment("The type that was introduced to the set later. Extra row for each type added live to the element")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('intersection_earlier_live_id')
                ->nullable()->default(null)
                ->comment("if the earlier was a live type")
                ->index()
                ->constrained('live_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('intersection_later_live_id')
                ->nullable()->default(null)
                ->comment("If the later was a live type")
                ->index()
                ->constrained('live_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->timestamps();
        });

        DB::statement("ALTER TABLE element_type_intersections Add COLUMN intersection_location_kind type_of_location NOT NULL default 'map';");

        DB::statement("CREATE TYPE type_of_intersection_category AS ENUM ('enclosed','enclosing', 'intersecting','not_set');");

        DB::statement("ALTER TABLE element_type_intersections Add COLUMN intersection_category type_of_intersection_category NOT NULL default 'not_set';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_type_intersections');
        DB::statement("DROP TYPE type_of_intersection_category;");
    }
};
