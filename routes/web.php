<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Frota\VeiculoController;
use App\Http\Controllers\Colaborador\ColaboradorController;
use App\Http\Controllers\Operacao\RotaController;
use App\Http\Controllers\Operacao\HorarioController;
use App\Http\Controllers\Operacao\EscalaController;
use App\Http\Controllers\Bilhetica\TarifaController;
use App\Http\Controllers\Bilhetica\BilheteController;
use App\Http\Controllers\Ocorrencia\OcorrenciaController;
use App\Http\Controllers\Manutencao\ManutencaoController;
use App\Http\Controllers\Financeiro\ReceitaController;
use App\Http\Controllers\Financeiro\DespesaController;
use App\Http\Controllers\Financeiro\FechoCaixaController;
use App\Http\Controllers\Relatorio\RelatorioController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\PerfilAcessoController;
use App\Http\Controllers\Admin\AuditoriaController;
use App\Http\Controllers\Admin\ConfiguracaoController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Publico\PublicController;

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Autenticação
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Rotas Protegidas (Autenticadas)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {


    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // API Routes para dados dinâmicos
    Route::prefix('api')->group(function () {
        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
        Route::get('/dashboard/ocupacao', [DashboardController::class, 'ocupacao']);
        Route::get('/dashboard/cumprimento', [DashboardController::class, 'cumprimentoHorarios']);
        Route::get('/ocorrencias/ultimas', [OcorrenciaController::class, 'ultimas'])->name('api.ocorrencias.ultimas');
    });

    // Perfil do usuário
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    /*
|--------------------------------------------------------------------------
| Módulo Notificações
|--------------------------------------------------------------------------
*/
Route::prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
    Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
});

    /*
    |--------------------------------------------------------------------------
    | Módulo Frota
    |--------------------------------------------------------------------------
    */
    Route::prefix('frota')->name('frota.')->group(function () {
        Route::get('/', [VeiculoController::class, 'index'])->name('index');
        Route::get('/create', [VeiculoController::class, 'create'])->name('create');
        Route::post('/', [VeiculoController::class, 'store'])->name('store');
        Route::get('/{veiculo}', [VeiculoController::class, 'show'])->name('show');
        Route::get('/{veiculo}/edit', [VeiculoController::class, 'edit'])->name('edit');
        Route::put('/{veiculo}', [VeiculoController::class, 'update'])->name('update');
        Route::delete('/{veiculo}', [VeiculoController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Módulo Colaboradores
    |--------------------------------------------------------------------------
    */
    Route::resource('colaboradores', ColaboradorController::class);

    /*
    |--------------------------------------------------------------------------
    | Módulo Operações - Rotas
    |--------------------------------------------------------------------------
    */
    Route::resource('rotas', RotaController::class);

    // ============================================
    // ROTAS DE HORÁRIOS - ADICIONAR ESTAS LINHAS
    // ============================================
    Route::prefix('rotas/{rota}/horarios')->name('rotas.horarios.')->group(function () {
        Route::get('/', [HorarioController::class, 'index'])->name('index');
        Route::get('/create', [HorarioController::class, 'create'])->name('create');
        Route::post('/', [HorarioController::class, 'store'])->name('store');
        Route::get('/{horario}/edit', [HorarioController::class, 'edit'])->name('edit');
        Route::put('/{horario}', [HorarioController::class, 'update'])->name('update');
        Route::delete('/{horario}', [HorarioController::class, 'destroy'])->name('destroy');
    });


    // Escalas
    Route::resource('escalas', EscalaController::class);
    Route::get('/api/escalas/diarias', [EscalaController::class, 'diarias'])->name('escalas.api.diarias');

   // Módulo Bilhética
Route::resource('tarifas', TarifaController::class);

Route::prefix('bilhetes')->name('bilhetes.')->group(function () {
    Route::get('/vender', [BilheteController::class, 'create'])->name('vender');
    Route::post('/vender', [BilheteController::class, 'vender'])->name('vender.post');
    Route::post('/validar', [BilheteController::class, 'validar'])->name('validar');
    Route::get('/validar', [BilheteController::class, 'validarForm'])->name('validar.form');
    Route::get('/{bilhete}', [BilheteController::class, 'show'])->name('show');
    Route::get('/qr/{bilhete}', [BilheteController::class, 'qrCode'])->name('qr');
    Route::get('/consultar/{codigo}', [BilheteController::class, 'consultar'])->name('consultar');
});

    /*
    |--------------------------------------------------------------------------
    | Módulo Ocorrências
    |--------------------------------------------------------------------------
    */
    Route::resource('ocorrencias', OcorrenciaController::class);

    /*
    |--------------------------------------------------------------------------
    | Módulo Manutenções
    |--------------------------------------------------------------------------
    */
    Route::resource('manutencoes', ManutencaoController::class);
    Route::post('/manutencoes/{manutencao}/executar', [ManutencaoController::class, 'executar'])->name('manutencoes.executar');

    /*
|--------------------------------------------------------------------------
| Módulo Financeiro
|--------------------------------------------------------------------------
*/
// Rotas de Receitas - PRIMEIRO as rotas específicas, DEPOIS o resource
Route::post('/receitas/consolidar', [ReceitaController::class, 'consolidar'])->name('receitas.consolidar');
Route::resource('receitas', ReceitaController::class);

// Rotas de Despesas
Route::resource('despesas', DespesaController::class);
Route::post('/despesas/{despesa}/aprovar', [DespesaController::class, 'aprovar'])->name('despesas.aprovar');

// Rotas de Fecho de Caixa
Route::post('/fecho-caixa', [FechoCaixaController::class, 'fechar'])->name('fecho-caixa.fechar');
Route::get('/fechos-caixa', [FechoCaixaController::class, 'historico'])->name('fechos-caixa.historico');
    /*
    |--------------------------------------------------------------------------
    | Módulo Relatórios
    |--------------------------------------------------------------------------
    */
    Route::prefix('relatorios')->name('relatorios.')->group(function () {
        Route::get('/cumprimento-horarios', [RelatorioController::class, 'cumprimentoHorarios'])->name('cumprimento-horarios');
        Route::get('/desempenho-motoristas', [RelatorioController::class, 'desempenhoMotoristas'])->name('desempenho-motoristas');
        Route::get('/ocupacao-veiculos', [RelatorioController::class, 'ocupacaoVeiculos'])->name('ocupacao-veiculos');
        Route::get('/fluxo-caixa', [RelatorioController::class, 'fluxoCaixa'])->name('fluxo-caixa');
    });


    /*
    |--------------------------------------------------------------------------
    | Módulo Administração
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {
        // Usuários
        Route::resource('usuarios', UsuarioController::class);
        Route::post('/usuarios/{usuario}/bloquear', [UsuarioController::class, 'bloquear'])->name('usuarios.bloquear');
        Route::post('/usuarios/{usuario}/ativar', [UsuarioController::class, 'ativar'])->name('usuarios.ativar');

        // Perfis de Acesso
        Route::resource('perfis', PerfilAcessoController::class);
        Route::post('/perfis/{perfil}/permissoes', [PerfilAcessoController::class, 'syncPermissoes'])->name('perfis.sync-permissoes');

        // Auditoria
        Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');
        Route::get('/auditoria/{log}', [AuditoriaController::class, 'show'])->name('auditoria.show');
        Route::post('/auditoria/export', [AuditoriaController::class, 'export'])->name('auditoria.export');
        Route::post('/auditoria/limpar', [AuditoriaController::class, 'limpar'])->name('auditoria.limpar');

        // Configurações
        Route::get('/configuracoes', [ConfiguracaoController::class, 'index'])->name('configuracoes.index');
        Route::put('/configuracoes', [ConfiguracaoController::class, 'update'])->name('configuracoes.update');
        Route::post('/configuracoes/clear-cache', [ConfiguracaoController::class, 'clearCache'])->name('configuracoes.clear-cache');
        Route::get('/configuracoes/backup', [ConfiguracaoController::class, 'backup'])->name('configuracoes.backup');
    });

    Route::get('/api/ultimas-vendas', [BilheteController::class, 'ultimasVendas'])->name('api.ultimas-vendas');
});

/*
|--------------------------------------------------------------------------
| API Pública (sem autenticação)
|--------------------------------------------------------------------------
*/
Route::prefix('api/public')->group(function () {
    Route::get('/rotas', [PublicController::class, 'rotas']);
    Route::get('/horarios', [PublicController::class, 'horarios']);
    Route::get('/tarifas', [PublicController::class, 'tarifas']);
    Route::post('/feedback', [PublicController::class, 'feedback']);
    Route::post('/planejar-viagem', [PublicController::class, 'planejarViagem']);
});
