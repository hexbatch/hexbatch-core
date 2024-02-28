<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('remote_meta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('remote_id')
                ->nullable(false)
                ->comment("The remote this header/key is for")
                ->index('idx_uri_has_remote_id')
                ->constrained('remotes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('remote_time_bounds_id')
                ->nullable()
                ->default(null)
                ->comment("Shows when this service is available in time, also mark downtimes here")
                ->index('idx_remote_meta_time_bounds_id')
                ->constrained('time_bounds')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('remote_location_bounds_id')
                ->nullable()
                ->default(null)
                ->comment("Shows where this service is available in time, also mark downtimes here")
                ->index('idx_remote_meta_map_bounds_id')
                ->constrained('location_bounds')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->json('remote_accepted_language_iso_codes')->default(null)->nullable()
                ->comment('an optional array of iso languages this server accepts');

            $table->json('remote_accepted_region_iso_codes')->default(null)->nullable()
                ->comment('an optional array of iso regions/countires/sub-admin regions this server works with');

            $table->timestamps();

            $table->text('remote_description')->default(null)->nullable()
                ->comment('if set, this port for the socket or url');

            $table->string('remote_terms_of_use_link')->default(null)->nullable()
                ->comment('optional url for terms of use');

            $table->string('remote_privacy_link')->default(null)->nullable()
                ->comment('optional url for privacy');

            $table->string('remote_about_link')->default(null)->nullable()
                ->comment('optional url for about this server');



        });



        DB::statement("ALTER TABLE remote_meta ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON remote_meta FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remote_meta');
    }
};
