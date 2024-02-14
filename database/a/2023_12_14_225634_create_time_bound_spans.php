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

            $table->integer('span_start')->nullable(false)
                ->comment('When this span starts (inclusive)');

            $table->integer('span_stop')->nullable(false)
                ->comment('When this span stops (inclusive)');

            $table->unique(['time_bound_id','span_start','span_stop'],'udx_unique_time_spans');
        });

        DB::statement('ALTER TABLE time_bound_spans ADD CONSTRAINT time_span_start_before_stop_constraint CHECK (span_start < span_stop)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_bound_spans');
    }
};
