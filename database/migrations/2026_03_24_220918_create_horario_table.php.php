<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHorarioTable extends Migration
{
    public function up()
    {
        Schema::create('horario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rota_id')->constrained('rota')->onDelete('cascade');
            $table->time('hora_partida');
            $table->time('hora_chegada');
            $table->string('dias_semana', 50)->comment('Ex: SEG,TER,QUA,QUI,SEX,SAB,DOM');
            $table->enum('tipo_dia', ['NORMAL', 'FERIADO', 'ESPECIAL'])->default('NORMAL');
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index(['rota_id', 'tipo_dia']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('horario');
    }
}
