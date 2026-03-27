<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOcorrenciaTable extends Migration
{
    public function up()
    {
        Schema::create('ocorrencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('veiculo_id')->constrained('veiculo')->onDelete('cascade');
            $table->foreignId('escala_id')->nullable()->constrained('escala')->onDelete('set null');
            $table->foreignId('colaborador_id')->nullable()->constrained('colaborador')->onDelete('set null');
            $table->enum('tipo', ['ACIDENTE', 'ATRASO', 'FALHA_MECANICA', 'ASSALTO', 'OUTRO']);
            $table->enum('gravidade', ['LEVE', 'MEDIA', 'GRAVE', 'CRITICA']);
            $table->text('descricao');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->dateTime('data_ocorrencia');
            $table->json('fotos_url')->nullable();
            $table->enum('status', ['ABERTA', 'EM_ANALISE', 'RESOLVIDA', 'CANCELADA'])->default('ABERTA');
            $table->boolean('sincronizado')->default(false);
            $table->dateTime('data_sincronizacao')->nullable();
            $table->timestamps();

            $table->index(['veiculo_id', 'data_ocorrencia']);
            $table->index('status');
            $table->index('tipo');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ocorrencia');
    }
}
