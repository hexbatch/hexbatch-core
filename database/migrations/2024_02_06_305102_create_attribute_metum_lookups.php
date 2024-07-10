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

        Schema::create('attribute_metum_lookups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('metum_lookup_attribute_id')
                ->nullable(false)
                ->comment("The attribute that uses this metum")
                ->index('idx_metum_lookup_attribute_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('metum_managed_by_attribute_id')
                ->nullable(false)
                ->comment("The attribute that manages this. ".
                    "Set to the ancestor|parent|child based on who set this in the inheritance chain. "
                )
                ->index('idx_metum_managed_by_attribute_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('metum_lookup_metum_id')
                ->nullable(false)
                ->comment("The metum that is used by the attribute")
                ->index('idx_metum_lookup_metum_id')
                ->constrained('attribute_meta')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->timestamps();
        });



        DB::statement("ALTER TABLE attribute_metum_lookups ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON attribute_metum_lookups FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_metum_lookups');
    }
};
