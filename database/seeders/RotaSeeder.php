<?php
namespace Database\Seeders;

use App\Models\Rota;
use Illuminate\Database\Seeder;

class RotaSeeder extends Seeder
{
    public function run()
    {
        $rotas = [
            [
                'nome' => 'Linha 110 - Morro Bento / Cidade Alta',
                'codigo' => 'L110',
                'descricao' => 'Percurso entre Morro Bento e Cidade Alta',
                'tipo' => 'URBANA',
                'distancia_total' => 12.5,
                'tempo_estimado' => 45,
                'ativa' => true,
                'empresa_responsavel' => 'TCUL',
            ],
            [
                'nome' => 'Linha 205 - Kilamba / Baía',
                'codigo' => 'L205',
                'descricao' => 'Percurso entre Kilamba e Baía',
                'tipo' => 'URBANA',
                'distancia_total' => 18.3,
                'tempo_estimado' => 60,
                'ativa' => true,
                'empresa_responsavel' => 'TCUL',
            ],
            [
                'nome' => 'Linha 307 - Zango / Luanda Sul',
                'codigo' => 'L307',
                'descricao' => 'Percurso entre Zango e Luanda Sul',
                'tipo' => 'INTERMUNICIPAL',
                'distancia_total' => 25.0,
                'tempo_estimado' => 75,
                'ativa' => true,
                'empresa_responsavel' => 'TCUL',
            ],
        ];

        foreach ($rotas as $rota) {
            Rota::updateOrCreate(
                ['codigo' => $rota['codigo']],
                $rota
            );
        }

        $this->command->info('Rotas criadas com sucesso!');
    }
}
