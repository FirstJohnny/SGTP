<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePecaTrocadaTable extends Migration
{
    public function up()
    {
        Schema::create('peca_trocada', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manutencao_id')->constrained('manutencao')->onDelete('cascade');
            $table->string('nome_peca', 150);
            $table->integer('quantidade');
            $table->decimal('preco_unitario', 15, 2);
            $table->integer('garantia_meses')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('peca_trocada');
    }
}
