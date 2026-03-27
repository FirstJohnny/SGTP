<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceitaTable extends Migration
{
    public function up()
    {
        Schema::create('receita', function (Blueprint $table) {
            $table->id();
            $table->date('data');
            $table->decimal('valor_total', 15, 2);
            $table->enum('origem', ['BILHETE', 'SUBSIDIO', 'CONTRATO', 'OUTROS']);
            $table->string('descricao', 255)->nullable();
            $table->boolean('consolidado')->default(false);
            $table->timestamps();

            $table->index(['data', 'consolidado']);
            $table->index('origem');
        });
    }

    public function down()
    {
        Schema::dropIfExists('receita');
    }
}
