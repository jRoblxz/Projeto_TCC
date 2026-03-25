<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/..._add_titulo_to_video_jobs_table.php
    public function up()
    {
        Schema::table('video_jobs', function (Blueprint $table) {
            // Adiciona a coluna 'titulo' logo depois do 'id'
            $table->string('titulo')->after('id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('video_jobs', function (Blueprint $table) {
            //
        });
    }
};
