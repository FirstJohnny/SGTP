<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColaboradorTable extends Migration
{
    public function up()
    {
        Schema::create('colaborador', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['MOTORISTA', 'COBRADOR', 'FISCAL', 'OUTRO']);
            $table->string('nome_completo', 150);
            $table->string('bi', 20)->unique();
            $table->string('numero_carta', 50)->nullable();
            $table->date('carta_validade')->nullable();
            $table->string('categoria_carta', 20)->nullable();
            $table->date('data_contratacao');
            $table->date('data_demissao')->nullable();
            $table->decimal('salario_base', 15, 2);
            $table->string('numero_seguranca_social', 30);
            $table->string('telefone', 20);
            $table->string('email', 150)->nullable();
            $table->string('foto_url', 255)->nullable();
            $table->string('emergencia_contato', 100);
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index('tipo');
            $table->index('bi');
            $table->index('data_contratacao');
        });
    }

    public function down()
    {
        Schema::dropIfExists('colaborador');
    }
}
