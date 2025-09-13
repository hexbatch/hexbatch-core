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
        /*
         * each type has all the exposed attributes, children attributes can cover up parent/ancestor attributes
         * type_id
         * attribute_id
         * attribute_from_pointer_id -- this table if attribute is inherited
         *
         *
         *
         * read from this table to see all the working attributes
         *   when it's joined with the visibilities for the set
         *    if it masked for a set, then it is also automatically masked for all children sets
         *    if it masked for no set, then it is automatically hidden from all sets
         *

         *
         * but then need to always join between the element_type_exposed_attributes
         *  and the element_type_set_visibilities, to get the current exposed attributes
         *
         * so, if I am in a set, and want to know, I get the visibilities for this set on up to all parents and then no set using a cte,
         *  then join that with the exposed_attributes using where to check if is_visible
         *  and join with the live_types and live_attributes to get the live attributes added onto an element
         */

        Schema::create('element_type_exposed_attributes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('exposed_type_id')
                ->nullable()->default(null)
                ->comment("This type has an exposed attribute")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('exposed_attribute_id')
                ->nullable()->default(null)
                ->comment("This is the exposed attribute. It can be read and written to by at least one group")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('exposed_parent_attribute_id')
                ->nullable()->default(null)
                ->comment("The parent attribute id")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('exposed_parent_type_id')
                ->nullable()->default(null)
                ->comment("The parent of the exposed has a type")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('inherits_exposed_id')
                ->nullable()->default(null)
                ->comment("Types inherit attributes from other types. This means this type attribute comes from an exposed parent attribute")
                ->index()
                ->constrained('element_type_exposed_attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamp('created_at')
                ->default(DB::raw('NOW()'))
                ->comment("When created");

            $table->timestamp('updated_at')
                ->default(null)
                ->nullable()
                ->comment("When updated");

        });

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON element_type_exposed_attributes FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_type_exposed_attributes');
    }
};
