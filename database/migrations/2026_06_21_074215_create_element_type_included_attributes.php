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


        Schema::create('element_type_included_attributes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('included_type_id')
                ->nullable(false)
                ->comment("This type has an exposed attribute")
                ->index()
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('included_attribute_id')
                ->nullable(false)
                ->comment("This is the exposed attribute. It can be read and written to by at least one group")
                ->index()
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();



            $table->timestamp('created_at')
                ->default(DB::raw('NOW()'))
                ->comment("When created");

            $table->timestamp('updated_at')
                ->default(null)
                ->nullable()
                ->comment("When updated");

            $table->unique(['included_type_id','included_attribute_id']);

        });

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON element_type_included_attributes FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('element_type_included_attributes');
    }
};
