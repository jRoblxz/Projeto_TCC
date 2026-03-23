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
        Schema::table('users', function (Blueprint $table) {
            // Se a coluna 'role' NÃO existir, aí sim ele cria!
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('treinador')->after('email');
            }
        });
    }

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('role');
    });
}
};
