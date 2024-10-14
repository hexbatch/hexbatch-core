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
        Schema::create('thing_settings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('setting_thing_id')
                ->nullable(false)
                ->comment("Settings belong to this thing, parents and children have their own setting, but the parent is the max limits that the child cannot exceed")
                ->unique()
                ->constrained('things')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->smallInteger('thing_pagination_size')->nullable()->default(null)
                ->comment('if set, then the path will use this for paginition');

            $table->smallInteger('thing_pagination_limit')->nullable()->default(null)
                ->comment('if set, then the count of pages in this tree will be calcuated, and if over then backoff applied to future pages');

            $table->smallInteger('thing_depth_limit')->nullable()->default(null)
                ->comment('if set, then the count of child levels in this tree will calculated, and if over, the subtree or tree returns false');

            $table->smallInteger('thing_rate_limit')->nullable()->default(null)
                ->comment('if set, then the count of actions this tree will calculated, and if over, the backoff happens');

            $table->smallInteger('thing_backoff_page_policy')->nullable()->default(null)
                ->comment('if set, then if over any limits here or in ancestors, then how long to backoff will be determined here');

            $table->smallInteger('thing_backoff_rate_policy')->nullable()->default(null)
                ->comment('if set, then if over any limits here or in ancestors, then how long to backoff will be determined here');

            $table->integer('thing_json_size_limit')->nullable()->default(null)
                ->comment('if set, then if any write or read over this size in utf8mb4 will result in an error');


            $table->timestamps();

        });

        DB::statement('ALTER TABLE thing_settings ADD CONSTRAINT unsigned_thing_pagination_size CHECK (thing_pagination_size IS NULL OR  thing_pagination_size > 0)');
        DB::statement('ALTER TABLE thing_settings ADD CONSTRAINT unsigned_thing_pagination_limit CHECK (thing_pagination_limit IS NULL OR  thing_pagination_limit > 0)');
        DB::statement('ALTER TABLE thing_settings ADD CONSTRAINT unsigned_thing_depth_limit CHECK (thing_depth_limit IS NULL OR  thing_depth_limit > 0)');
        DB::statement('ALTER TABLE thing_settings ADD CONSTRAINT unsigned_thing_rate_limit CHECK (thing_rate_limit IS NULL OR  thing_rate_limit > 0)');
        DB::statement('ALTER TABLE thing_settings ADD CONSTRAINT unsigned_thing_json_size_limit CHECK (thing_json_size_limit IS NULL OR  thing_json_size_limit > 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thing_settings');
    }
};
