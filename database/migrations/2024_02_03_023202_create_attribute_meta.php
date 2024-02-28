<?php

use App\Models\AttributeMetum;
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
        Schema::create('attribute_meta', function (Blueprint $table) {
            $table->id();

            $table->foreignId('meta_parent_attribute_id')
                ->nullable(false)
                ->comment("The attribute this rule is for")
                ->index('idx_meta_attribute_parent_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();



            $table->timestamps();
        });

        DB::statement("CREATE TYPE type_of_attribute_metum AS ENUM (
            'none','author','copywrite','url','rating','lang','mime_type'
            );");

        DB::statement("ALTER TABLE attribute_meta Add COLUMN meta_type type_of_attribute_metum NOT NULL default 'none';");

        DB::statement("ALTER TABLE attribute_meta ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON attribute_meta FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        Schema::table('attribute_meta', function (Blueprint $table) {
            $table->index(['meta_parent_attribute_id','meta_type'],'idx_attr_meta_type');
            $table->string('meta_value')->nullable()->default(null)->comment("the value of the meta");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_meta');
        DB::statement("DROP TYPE type_of_attribute_metum;");
    }
};
