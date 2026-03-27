<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateValidacaoBilheteTable extends Migration
{
    public function up()
    {
        Schema::create('validacao_bilhete', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bilhete_id')->constrained('bilhete')->onDelete('cascade');
            $table->foreignId('veiculo_id')->constrained('veiculo')->onDelete('cascade');
            $table->foreignId('escala_id')->constrained('escala')->onDelete('cascade');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->dateTime('timestamp');
            $table->enum('metodo', ['QRCODE', 'BARRAS', 'NFC', 'MANUAL', 'OUTRO']);
            $table->timestamps();

            $table->index('bilhete_id');
            $table->index(['veiculo_id', 'timestamp']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('validacao_bilhete');
    }
}
