<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDespesaTable extends Migration
{
    public function up()
    {
        Schema::create('despesa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('veiculo_id')->nullable()->constrained('veiculo')->onDelete('set null');
            $table->enum('tipo', ['MANUTENCAO', 'COMBUSTIVEL', 'SEGURO', 'MULTA', 'SALARIO', 'OUTRO']);
            $table->decimal('valor', 15, 2);
            $table->date('data');
            $table->string('descricao', 255)->nullable();
            $table->string('documento_url', 255)->nullable();
            $table->foreignId('aprovado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('aprovado')->default(false);
            $table->timestamps();

            $table->index(['data', 'tipo']);
            $table->index('veiculo_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('despesa');
    }
}
