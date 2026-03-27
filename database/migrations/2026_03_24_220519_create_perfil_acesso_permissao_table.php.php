<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerfilAcessoPermissaoTable extends Migration
{
    public function up()
    {
        Schema::create('perfil_acesso_permissao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perfil_id')->constrained('perfil_acesso')->onDelete('cascade');
            $table->foreignId('permissao_id')->constrained('permissao')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['perfil_id', 'permissao_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('perfil_acesso_permissao');
    }
}
