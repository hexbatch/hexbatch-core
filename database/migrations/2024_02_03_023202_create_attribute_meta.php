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
            'none',
            'description','name','standard_family',
            'author','copywrite','url','rating','internal'
            );");

        DB::statement("ALTER TABLE attribute_meta Add COLUMN meta_type type_of_attribute_metum NOT NULL default 'none';");

        DB::statement("ALTER TABLE attribute_meta ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON attribute_meta FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");

        Schema::table('attribute_meta', function (Blueprint $table) {

            $table->string('meta_iso_lang',10)->nullable(false)->index()
                ->comment("The iso language code")->default(AttributeMetum::ANY_LANGUAGE);


            $table->index(['meta_type'],'idx_meta_type');
            $table->index(['meta_type','meta_iso_lang'],'idx_meta_type_with_lang');
            $table->index(['meta_parent_attribute_id','meta_type'],'idx_meta_type_of_attribute');
            $table->unique(['meta_parent_attribute_id','meta_iso_lang','meta_type'],'udx_one_meta_type_per_lang_per_attribute');

            $table->string('meta_mime_type',255)->nullable()->default(null)
                ->comment("some meta can be markdown or html or plain text");

            $table->text('meta_value')->nullable(false)->comment("the value of the meta");

            $table->jsonb('meta_json')->nullable()->default(null)
                ->comment('data stored with the meta value');
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
