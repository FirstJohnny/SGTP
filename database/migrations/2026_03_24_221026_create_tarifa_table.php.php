<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTarifaTable extends Migration
{
    public function up()
    {
        Schema::create('tarifa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rota_id')->constrained('rota')->onDelete('cascade');
            $table->enum('tipo_passageiro', ['ADULTO', 'ESTUDANTE', 'IDOSO', 'OUTRO']);
            $table->decimal('valor', 10, 2);
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->boolean('ativa')->default(true);
            $table->timestamps();

            $table->index(['rota_id', 'tipo_passageiro', 'ativa']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tarifa');
    }
}
