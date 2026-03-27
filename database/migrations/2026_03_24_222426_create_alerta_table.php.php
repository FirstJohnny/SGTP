<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertaTable extends Migration
{
    public function up()
    {
        Schema::create('alerta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('veiculo_id')->constrained('veiculo')->onDelete('cascade');
            $table->enum('tipo', ['MANUTENCAO', 'SEGURANCA', 'GPS', 'DOCUMENTO', 'OUTRO']);
            $table->enum('gravidade', ['LEVE', 'MEDIA', 'GRAVE']);
            $table->text('mensagem');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->dateTime('timestamp');
            $table->boolean('resolvido')->default(false);
            $table->foreignId('resolvido_por')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('data_resolucao')->nullable();
            $table->timestamps();

            $table->index(['veiculo_id', 'resolvido']);
            $table->index('tipo');
            $table->index('gravidade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('alerta');
    }
}
