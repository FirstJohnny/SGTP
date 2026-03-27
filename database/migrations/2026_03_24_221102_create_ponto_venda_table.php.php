<?php
// database/migrations/2024_01_01_000012_create_ponto_venda_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePontoVendaTable extends Migration
{
    public function up()
    {
        Schema::create('ponto_venda', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('endereco', 255)->nullable();
            $table->foreignId('operador_responsavel')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('stock_bilhetes')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index(['latitude', 'longitude']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('ponto_venda');
    }
}
