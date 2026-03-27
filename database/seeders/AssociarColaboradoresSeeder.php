<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Colaborador;
use Illuminate\Database\Seeder;

class AssociarColaboradoresSeeder extends Seeder
{
    public function run()
    {
        // Associar fiscal ao usuário fiscal
        $fiscal = Colaborador::where('email', 'antonio.lukamba@sgtp.ao')->first();
        $userFiscal = User::where('email', 'fiscal@sgtp.ao')->first();
        
        if ($fiscal && $userFiscal) {
            $fiscal->user_id = $userFiscal->id;
            $fiscal->save();
            $this->command->info('Fiscal associado ao usuário!');
        }
        
        // Associar motorista ao usuário (se existir)
        $motorista = Colaborador::where('email', 'joao.chimuco@sgtp.ao')->first();
        
        if ($motorista) {
            // Verificar se o usuário já existe
            $userMotorista = User::where('email', 'joao.chimuco@sgtp.ao')->first();
            
            if (!$userMotorista) {
                // Criar usuário com tipo válido
                $userMotorista = User::create([
                    'name' => 'João Chimuco',
                    'email' => 'joao.chimuco@sgtp.ao',
                    'password' => bcrypt('motorista123'),
                    'tipo_usuario' => 'FISCAL', // Usar um tipo válido do ENUM
                    'status' => 'ATIVO',
                    'bi' => '001234567LA001',
                    'telefone' => '+244 923 456 789',
                ]);
                $this->command->info('Usuário motorista criado!');
            }
            
            $motorista->user_id = $userMotorista->id;
            $motorista->save();
            $this->command->info('Motorista João Chimuco associado ao usuário!');
        }
        
        $this->command->info('Associações de colaboradores concluídas!');
    }
}