<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150)->default('Anônimo');
            $table->string('email', 150)->nullable();
            $table->enum('tipo', ['ELOGIO', 'SUGESTAO', 'RECLAMACAO', 'DUVIDA']);
            $table->text('mensagem');
            $table->foreignId('rota_id')->nullable()->constrained('rota')->onDelete('set null');
            $table->foreignId('veiculo_id')->nullable()->constrained('veiculo')->onDelete('set null');
            $table->string('ip_address', 45)->nullable();
            $table->dateTime('data_envio');
            $table->boolean('lido')->default(false);
            $table->text('resposta')->nullable();
            $table->dateTime('respondido_em')->nullable();
            $table->timestamps();
            
            $table->index(['tipo', 'data_envio']);
            $table->index('lido');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};