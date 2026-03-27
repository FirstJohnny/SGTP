<?php
namespace Database\Seeders;

use App\Models\PontoParagem;
use Illuminate\Database\Seeder;

class PontoParagemSeeder extends Seeder
{
    public function run()
    {
        $pontos = [
            [
                'nome' => 'Terminal Morro Bento',
                'latitude' => -8.838333,
                'longitude' => 13.234444,
                'endereco' => 'Morro Bento, Luanda',
                'tipo' => 'TERMINAL',
                'tem_abrigo' => true,
                'tem_bilheteira' => true,
            ],
            [
                'nome' => 'Cidade Alta',
                'latitude' => -8.813889,
                'longitude' => 13.233611,
                'endereco' => 'Cidade Alta, Luanda',
                'tipo' => 'TERMINAL',
                'tem_abrigo' => true,
                'tem_bilheteira' => true,
            ],
            [
                'nome' => 'Largo das Escolas',
                'latitude' => -8.826111,
                'longitude' => 13.236111,
                'endereco' => 'Largo das Escolas, Luanda',
                'tipo' => 'PONTO',
                'tem_abrigo' => true,
                'tem_bilheteira' => false,
            ],
            [
                'nome' => 'Mutamba',
                'latitude' => -8.813333,
                'longitude' => 13.238333,
                'endereco' => 'Mutamba, Luanda',
                'tipo' => 'PONTO',
                'tem_abrigo' => true,
                'tem_bilheteira' => true,
            ],
        ];

        foreach ($pontos as $ponto) {
            PontoParagem::updateOrCreate(
                ['nome' => $ponto['nome']],
                $ponto
            );
        }

        $this->command->info('Pontos de paragem criados com sucesso!');
    }
}
