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
            // Se a coluna 'passe' NÃO existir, ele cria todas elas.
            // Se já existir, ele pula e não dá erro!
            if (!Schema::hasColumn('avaliacoes', 'passe')) {
                $table->decimal('passe', 5, 2)->nullable();
                $table->decimal('visao_jogo', 5, 2)->nullable();
                $table->decimal('tecnica', 5, 2)->nullable();
                $table->decimal('finalizacao', 5, 2)->nullable();
                $table->decimal('velocidade', 5, 2)->nullable();
                $table->integer('condicionamento')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('avaliacoes', function (Blueprint $table) {
            $table->dropColumn([
                'passe', 
                'visao_jogo', 
                'tecnica', 
                'finalizacao', 
                'velocidade', 
                'condicionamento'
            ]);
        });
    }
};
