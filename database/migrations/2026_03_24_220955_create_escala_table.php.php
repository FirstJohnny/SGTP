<?php
// database/migrations/2024_01_01_000010_create_escala_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEscalaTable extends Migration
{
    public function up()
    {
        Schema::create('escala', function (Blueprint $table) {
            $table->id();
            $table->foreignId('veiculo_id')->constrained('veiculo')->onDelete('cascade');
            $table->foreignId('motorista_id')->constrained('colaborador')->onDelete('restrict');
            $table->foreignId('cobrador_id')->nullable()->constrained('colaborador')->onDelete('restrict');
            $table->foreignId('rota_id')->constrained('rota')->onDelete('cascade');
            $table->date('data');
            $table->time('hora_inicio');
            $table->time('hora_fim');
            $table->time('hora_inicio_real')->nullable();
            $table->time('hora_fim_real')->nullable();
            $table->integer('km_inicial')->nullable();
            $table->integer('km_final')->nullable();
            $table->enum('status', ['PENDENTE', 'EM_ANDAMENTO', 'FINALIZADA', 'CANCELADA'])->default('PENDENTE');
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->index(['data', 'status']);
            $table->index('motorista_id');
            $table->index('veiculo_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('escala');
    }
}
