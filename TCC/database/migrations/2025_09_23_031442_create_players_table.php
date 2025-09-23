<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->decimal('altura', 3, 2); // Ex: 1.87
            $table->integer('peso'); // Em kg
            $table->string('pe_dominante'); // Direito, Esquerdo, Ambos
            $table->string('posicao', 3); // ATA, MEI, DEF, etc.
            $table->integer('rating')->nullable(); // 0-100
            $table->text('avaliacao')->nullable();
            $table->string('foto')->nullable(); // Nome do arquivo da foto
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('players');
    }
};