<?php
// database/migrations/2024_01_01_000004_create_veiculo_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVeiculoTable extends Migration
{
    public function up()
    {
        Schema::create('veiculo', function (Blueprint $table) {
            $table->id();
            $table->string('placa', 20)->unique();
            $table->string('chassi', 50)->unique();
            $table->string('marca', 100);
            $table->string('modelo', 100);
            $table->integer('ano_fabricado');
            $table->string('cor', 50);
            $table->integer('lotacao');
            $table->enum('tipo_combustivel', ['DIESEL', 'GASOLINA', 'ELETRICO', 'HIBRIDO']);
            $table->float('consumo_medio')->nullable();
            $table->integer('km_atual')->default(0);
            $table->date('data_aquisicao');
            $table->enum('status', ['ATIVO', 'MANUTENCAO', 'INATIVO'])->default('ATIVO');
            $table->date('ultima_inspecao')->nullable();
            $table->date('proxima_inspecao')->nullable();
            $table->date('seguro_validade');
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('placa');
            $table->index('status');
            $table->index('seguro_validade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('veiculo');
    }
}
