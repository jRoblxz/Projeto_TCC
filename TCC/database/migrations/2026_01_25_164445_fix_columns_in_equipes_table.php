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
    Schema::table('Equipes', function (Blueprint $table) {
        
        // 1. Tratamento da coluna 'nome'
        if (Schema::hasColumn('Equipes', 'nome')) {
            // Se 'nome' JÁ EXISTE, verificamos se 'nome_equipe' também existe.
            // Se existir, removemos 'nome_equipe' para não ter duplicidade de dados/colunas.
            if (Schema::hasColumn('Equipes', 'nome_equipe')) {
                $table->dropColumn('nome_equipe');
            }
        } else {
            // Se 'nome' NÃO EXISTE, verificamos se podemos renomear 'nome_equipe'
            if (Schema::hasColumn('Equipes', 'nome_equipe')) {
                $table->renameColumn('nome_equipe', 'nome');
            } else {
                // Se nem 'nome' nem 'nome_equipe' existem, criamos do zero
                $table->string('nome')->nullable();
            }
        }
        
        // 2. Garante que peneira_id existe
        if (!Schema::hasColumn('Equipes', 'peneira_id')) {
            $table->foreignId('peneira_id')->constrained('peneiras')->onDelete('cascade');
        }
        
        // 3. Adiciona timestamps se não tiver
        if (!Schema::hasColumn('Equipes', 'created_at')) {
            $table->timestamps();
        }
    });
}

public function down()
{
    Schema::table('Equipes', function (Blueprint $table) {
        if (Schema::hasColumn('Equipes', 'nome')) {
            $table->renameColumn('nome', 'nome_equipe');
        }
    });
}
};
