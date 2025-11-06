<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
           CREATE OR REPLACE FUNCTION bigint_or_null(str text)
            RETURNS bigint AS $$
            BEGIN
              RETURN str::bigint;
            EXCEPTION WHEN invalid_text_representation THEN
              RETURN NULL;
            END;
            $$ LANGUAGE plpgsql;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            DROP FUNCTION IF EXISTS bigint_or_null();
        ");
    }
};
