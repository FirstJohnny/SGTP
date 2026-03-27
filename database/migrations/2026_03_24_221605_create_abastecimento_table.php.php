<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbastecimentoTable extends Migration
{
    public function up()
    {
        Schema::create('abastecimento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('veiculo_id')->constrained('veiculo')->onDelete('cascade');
            $table->foreignId('motorista_id')->nullable()->constrained('colaborador')->onDelete('set null');
            $table->date('data');
            $table->integer('odometro');
            $table->float('litros');
            $table->decimal('valor_total', 15, 2);
            $table->decimal('preco_litro', 10, 2);
            $table->string('posto', 150);
            $table->enum('tipo_combustivel', ['DIESEL', 'GASOLINA', 'ELETRICO', 'HIBRIDO']);
            $table->string('comprovativo_url', 255)->nullable();
            $table->timestamps();

            $table->index(['veiculo_id', 'data']);
            $table->index('odometro');
        });
    }

    public function down()
    {
        Schema::dropIfExists('abastecimento');
    }
}
