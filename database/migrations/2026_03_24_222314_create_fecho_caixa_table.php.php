<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFechoCaixaTable extends Migration
{
    public function up()
    {
        Schema::create('fecho_caixa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operador_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('data_fecho');
            $table->decimal('valor_esperado', 15, 2);
            $table->decimal('valor_apurado', 15, 2);
            $table->decimal('diferenca', 15, 2);
            $table->text('observacoes')->nullable();
            $table->enum('status', ['ABERTO', 'FECHADO', 'CONFERIDO'])->default('ABERTO');
            $table->timestamps();

            $table->index(['operador_id', 'data_fecho']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('fecho_caixa');
    }
}
