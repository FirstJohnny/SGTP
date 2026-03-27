<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogAuditoriaTable extends Migration
{
    public function up()
    {
        Schema::create('log_auditoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->string('acao', 100);
            $table->string('entidade', 100);
            $table->unsignedBigInteger('entidade_id')->nullable();
            $table->json('dados_anteriores')->nullable();
            $table->json('dados_novos')->nullable();
            $table->string('ip_address', 45);
            $table->string('user_agent')->nullable();
            $table->dateTime('timestamp');
            $table->timestamps();

            $table->index(['usuario_id', 'timestamp']);
            $table->index('entidade');
            $table->index('acao');
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_auditoria');
    }
}
