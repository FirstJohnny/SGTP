<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRotaTable extends Migration
{
    public function up()
    {
        Schema::create('rota', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->string('codigo', 50)->unique();
            $table->text('descricao')->nullable();
            $table->enum('tipo', ['URBANA', 'INTERMUNICIPAL', 'RODOVIARIA', 'ESCOLAR']);
            $table->float('distancia_total');
            $table->integer('tempo_estimado')->nullable()->comment('Tempo em minutos');
            $table->json('trajeto_geojson')->nullable();
            $table->boolean('ativa')->default(true);
            $table->string('empresa_responsavel', 150)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('codigo');
            $table->index('ativa');
        });
    }

    public function down()
    {
        Schema::dropIfExists('rota');
    }
}
