<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pessoas', function (Blueprint $table) {
            $table->date('data_nascimento')->nullable()->change();
            $table->string('cidade')->nullable()->change();
            $table->string('cpf')->nullable()->change();
            $table->string('rg')->nullable()->change();
            $table->string('telefone')->nullable()->change();
            $table->string('senha')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('pessoas', function (Blueprint $table) {
            $table->date('data_nascimento')->nullable(false)->change();
            $table->string('cidade')->nullable(false)->change();
            $table->string('cpf')->nullable(false)->change();
            $table->string('rg')->nullable(false)->change();
            $table->string('telefone')->nullable(false)->change();
            $table->string('senha')->nullable(false)->change();
        });
    }
};