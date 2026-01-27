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
        // Verifica se a coluna não existe antes de criar
        if (!Schema::hasColumn('Equipes', 'nome')) {
            $table->string('nome')->nullable();
        }
        if (!Schema::hasColumn('Equipes', 'peneira_id')) {
            $table->foreignId('peneira_id')->constrained('peneiras')->onDelete('cascade');
        }
        // Garante que timestamps existam (created_at, updated_at) se seu model usa
        if (!Schema::hasColumn('Equipes', 'created_at')) {
            $table->timestamps();
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('Equipes', function (Blueprint $table) {
            //
        });
    }
};
