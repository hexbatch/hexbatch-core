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


        Schema::create('set_member_intersection_changes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('hosting_intersection_id')
                ->nullable()->default(null)
                ->comment("The set member that was passive")
                ->index()
                ->constrained('element_type_intersections')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('moved_intersection_id')
                ->nullable()->default(null)
                ->comment("The set member that was moved or changed")
                ->index()
                ->constrained('element_type_intersections')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


        });

        DB::statement("CREATE TYPE type_of_intersection_state AS ENUM ('enclosed','disjoined', 'overlapping');");

        DB::statement(/** @lang text */ "ALTER TABLE set_member_intersection_changes Add COLUMN from_intersection_state type_of_intersection_state NOT NULL ;");
        DB::statement(/** @lang text */ "ALTER TABLE set_member_intersection_changes Add COLUMN to_intersection_state type_of_intersection_state NOT NULL ;");



        Schema::table('set_member_intersection_changes', function (Blueprint $table) {
            $table->timestamp('created_at')
                ->default(DB::raw('NOW()'))
                ->comment("When created");

            $table->timestamp('updated_at')
                ->default(null)
                ->nullable()
                ->comment("When updated");
        });

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON set_member_intersection_changes FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('set_member_intersection_changes');
        DB::statement("DROP TYPE type_of_intersection_state;");
    }
};
