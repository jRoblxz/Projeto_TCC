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
        $table->decimal('tecnica', 3, 1)->nullable();
        $table->decimal('condicionamento', 3, 1)->nullable();
        $table->decimal('finalizacao', 3, 1)->nullable();
        $table->decimal('velocidade', 3, 1)->nullable();
        $table->decimal('posicionamento', 3, 1)->nullable();
        $table->decimal('cabeceio', 3, 1)->nullable();
    });
}

public function down()
{
    Schema::table('avaliacoes', function (Blueprint $table) {
        $table->dropColumn(['tecnica', 'condicionamento', 'finalizacao', 'velocidade', 'posicionamento', 'cabeceio']);
    });
}
};
