<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManutencaoTable extends Migration
{
    public function up()
    {
        Schema::create('manutencao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('veiculo_id')->constrained('veiculo')->onDelete('cascade');
            $table->foreignId('ocorrencia_id')->nullable()->constrained('ocorrencia')->onDelete('set null');
            $table->enum('tipo', ['PREVENTIVA', 'CORRETIVA', 'EMERGENCIAL']);
            $table->text('descricao');
            $table->date('data_agendamento')->nullable();
            $table->dateTime('data_inicio')->nullable();
            $table->dateTime('data_fim')->nullable();
            $table->string('oficina', 150);
            $table->decimal('custo_pecas', 15, 2)->default(0);
            $table->decimal('custo_mao_obra', 15, 2)->default(0);
            $table->decimal('custo_total', 15, 2);
            $table->text('observacoes')->nullable();
            $table->enum('status', ['AGENDADA', 'EM_EXECUCAO', 'CONCLUIDA', 'CANCELADA'])->default('AGENDADA');
            $table->timestamps();

            $table->index(['veiculo_id', 'status']);
            $table->index('data_agendamento');
        });
    }

    public function down()
    {
        Schema::dropIfExists('manutencao');
    }
}
