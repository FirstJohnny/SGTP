<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissaoSeeder::class,
            PerfilAcessoSeeder::class,   // Este agora vai associar permissões aos perfis
            UsersTableSeeder::class,      // Este agora vai associar perfis aos usuários
            VeiculoSeeder::class,
            ColaboradorSeeder::class,
            RotaSeeder::class,
            PontoParagemSeeder::class,
            TarifaSeeder::class,
            AssociarColaboradoresSeeder::class, // Associar colaboradores aos usuários
        ]);

        $this->command->info('Todos os seeders executados com sucesso!');
    }
}