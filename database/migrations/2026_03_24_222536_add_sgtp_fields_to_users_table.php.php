<?php
// database/migrations/2024_01_01_000025_add_sgtp_fields_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSgtpFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('bi', 20)->nullable()->unique()->after('email');
            $table->string('telefone', 20)->nullable()->after('bi');
            $table->enum('tipo_usuario', [
                'ADMIN',
                'GESTOR_OPERACOES',
                'GESTOR_FROTA',
                'FISCAL',
                'OPERADOR_BILHETICA',
                'FINANCEIRO'
            ])->default('OPERADOR_BILHETICA')->after('telefone');
            $table->enum('status', ['ATIVO', 'INATIVO', 'BLOQUEADO'])->default('ATIVO')->after('tipo_usuario');
            $table->dateTime('ultimo_acesso')->nullable()->after('status');
            $table->foreignId('perfil_acesso_id')->nullable()->constrained('perfil_acesso')->onDelete('set null')->after('ultimo_acesso');
            $table->boolean('two_factor_enabled')->default(false)->after('perfil_acesso_id');
            $table->string('two_factor_secret', 255)->nullable()->after('two_factor_enabled');

            $table->index('tipo_usuario');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['perfil_acesso_id']);
            $table->dropColumn([
                'bi',
                'telefone',
                'tipo_usuario',
                'status',
                'ultimo_acesso',
                'perfil_acesso_id',
                'two_factor_enabled',
                'two_factor_secret'
            ]);
        });
    }
}
