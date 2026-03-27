<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBilheteTable extends Migration
{
    public function up()
    {
        Schema::create('bilhete', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_barras', 100)->unique();
            $table->foreignId('tarifa_id')->constrained('tarifa')->onDelete('restrict');
            $table->decimal('valor_pago', 10, 2);
            $table->dateTime('data_venda');
            $table->foreignId('ponto_venda_id')->constrained('ponto_venda')->onDelete('restrict');
            $table->foreignId('operador_id')->constrained('users')->onDelete('restrict');
            $table->enum('status', ['VALIDO', 'UTILIZADO', 'CANCELADO', 'EXPIRADO'])->default('VALIDO');
            $table->date('data_validade');
            $table->enum('forma_pagamento', ['DINHEIRO', 'CARTAO', 'PIX', 'TRANSFERENCIA', 'OUTRO']);
            $table->timestamps();

            $table->index('codigo_barras');
            $table->index('status');
            $table->index('data_validade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bilhete');
    }
}
