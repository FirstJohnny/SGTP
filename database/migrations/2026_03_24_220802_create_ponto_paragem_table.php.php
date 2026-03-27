<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePontoParagemTable extends Migration
{
    public function up()
    {
        Schema::create('ponto_paragem', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('endereco', 255)->nullable();
            $table->enum('tipo', ['PONTO', 'TERMINAL', 'OUTRO'])->default('PONTO');
            $table->boolean('tem_abrigo')->default(false);
            $table->boolean('tem_bilheteira')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['latitude', 'longitude']);
            $table->index('tipo');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ponto_paragem');
    }
}
