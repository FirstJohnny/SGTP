<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRastreamentoGpsTable extends Migration
{
    public function up()
    {
        Schema::create('rastreamento_gps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('veiculo_id')->constrained('veiculo')->onDelete('cascade');
            $table->foreignId('escala_id')->nullable()->constrained('escala')->onDelete('set null');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->float('velocidade')->nullable()->comment('Velocidade em km/h');
            $table->integer('direcao')->nullable()->comment('Direção em graus (0-360)');
            $table->boolean('ignicao')->default(false);
            $table->integer('odometro_gps')->nullable();
            $table->dateTime('timestamp');
            $table->float('precisao')->nullable()->comment('Precisão em metros');
            $table->timestamps();

            $table->index(['veiculo_id', 'timestamp']);
            $table->index('escala_id');
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('rastreamento_gps');
    }
}
