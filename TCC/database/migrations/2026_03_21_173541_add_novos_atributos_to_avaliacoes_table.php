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
    Schema::table('avaliacoes', function (Blueprint $table) {
        $table->decimal('reflexo', 3, 1)->nullable();
        $table->decimal('saida_goleiro', 3, 1)->nullable();
        $table->decimal('jogo_aereo', 3, 1)->nullable();
        $table->decimal('um_contra_um', 3, 1)->nullable();
        $table->decimal('fisico', 3, 1)->nullable();
        $table->decimal('marcacao', 3, 1)->nullable();
        $table->decimal('desarme', 3, 1)->nullable();
        $table->decimal('passe', 3, 1)->nullable();
        $table->decimal('cruzamento', 3, 1)->nullable();
        $table->decimal('visao_jogo', 3, 1)->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('avaliacoes', function (Blueprint $table) {
            //
        });
    }
};
