<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePontoRotaTable extends Migration
{
    public function up()
    {
        Schema::create('ponto_rota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rota_id')->constrained('rota')->onDelete('cascade');
            $table->foreignId('ponto_paragem_id')->constrained('ponto_paragem')->onDelete('cascade');
            $table->integer('ordem');
            $table->integer('tempo_estimado_chegada')->nullable()->comment('Tempo em minutos desde o início');
            $table->float('distancia_desde_inicio')->nullable()->comment('Distância em km desde o início');
            $table->timestamps();

            $table->unique(['rota_id', 'ponto_paragem_id']);
            $table->index('ordem');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ponto_rota');
    }
}
