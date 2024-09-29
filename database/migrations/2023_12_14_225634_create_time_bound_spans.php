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
        Schema::create('time_bound_spans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('time_bound_id')
                ->nullable()
                ->default(null)
                ->comment("The time bounds this is for")
                ->index('idx_time_bound_span_parent')
                ->constrained('time_bounds')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

        });
        DB::statement(/** @lang text */ "
                ALTER TABLE time_bound_spans
                ADD COLUMN time_slice_range daterange NOT NULL;
        ");

        DB::statement("
            CREATE INDEX ON time_bound_spans USING GIST (time_slice_range);
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_bound_spans');
    }
};
