<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hex_errors', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->integer('hex_error_code')->default(null)->nullable()
                ->comment('The code of the exception');

            $table->integer('hex_error_line')->default(null)->nullable()
                ->comment('The line of the exception');

            $table->float('hex_code_version')->default(null)->nullable()
                ->comment('The line of the exception');

            $table->text('hex_error_message')->default(null)->nullable()
                ->comment('the message');

            $table->jsonb('hex_error_trace')->default(null)->nullable()
                ->comment('the stack trace');

            $table->string('hex_error_file')->default(null)->nullable()
                ->comment('The file of the exception');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hex_errors');
    }
};
