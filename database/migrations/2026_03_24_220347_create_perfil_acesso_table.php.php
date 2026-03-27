<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerfilAcessoTable extends Migration
{
    public function up()
    {
        Schema::create('perfil_acesso', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED auto-increment
            $table->string('nome', 100);
            $table->text('descricao')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('perfil_acesso');
    }
}
