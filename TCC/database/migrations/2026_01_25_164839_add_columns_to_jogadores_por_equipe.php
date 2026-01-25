<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('JogadoresPorEquipe', function (Blueprint $table) {
        if (!Schema::hasColumn('JogadoresPorEquipe', 'posicao_campo_x')) {
            $table->integer('posicao_campo_x')->nullable();
        }
        if (!Schema::hasColumn('JogadoresPorEquipe', 'posicao_campo_y')) {
            $table->integer('posicao_campo_y')->nullable();
        }
        if (!Schema::hasColumn('JogadoresPorEquipe', 'titular')) {
            $table->boolean('titular')->default(false);
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('JogadoresPorEquipe', function (Blueprint $table) {
            //
        });
    }
};
