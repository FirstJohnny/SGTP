<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissaoTable extends Migration
{
    public function up()
    {
        Schema::create('permissao', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('descricao', 255)->nullable();
            $table->string('modulo', 100);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('permissao');
    }
}
