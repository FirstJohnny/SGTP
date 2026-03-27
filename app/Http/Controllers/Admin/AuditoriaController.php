<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAuditoria;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AuditoriaController extends Controller
{

    /**
     * Listar logs de auditoria
     */
    public function index(Request $request)
    {
        $query = LogAuditoria::with('usuario');

        // Filtros
        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        if ($request->filled('acao')) {
            $query->where('acao', $request->acao);
        }

        if ($request->filled('entidade')) {
            $query->where('entidade', 'like', "%{$request->entidade}%");
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('timestamp', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('timestamp', '<=', $request->data_fim);
        }

        if ($request->filled('ip_address')) {
            $query->where('ip_address', $request->ip_address);
        }

        $logs = $query->orderBy('timestamp', 'desc')->paginate(30);

        // Estatísticas
        $estatisticas = [
            'total' => LogAuditoria::count(),
            'hoje' => LogAuditoria::whereDate('timestamp', Carbon::today())->count(),
            'esta_semana' => LogAuditoria::whereBetween('timestamp', [Carbon::now()->startOfWeek(), Carbon::now()])->count(),
            'por_acao' => LogAuditoria::select('acao', DB::raw('count(*) as total'))
                ->groupBy('acao')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get(),
        ];

        $usuarios = User::select('id', 'name', 'email')->orderBy('name')->get();
        $acoes = LogAuditoria::distinct('acao')->pluck('acao');

        return view('admin.auditoria.index', compact('logs', 'estatisticas', 'usuarios', 'acoes'));
    }

    /**
     * Exibir detalhes do log
     */
    public function show(LogAuditoria $log)
    {
        return view('admin.auditoria.show', compact('log'));
    }

    /**
     * Exportar logs para CSV/Excel
     */
    public function export(Request $request)
    {
        $query = LogAuditoria::with('usuario');

        // Aplicar os mesmos filtros
        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->usuario_id);
        }

        if ($request->filled('acao')) {
            $query->where('acao', $request->acao);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('timestamp', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('timestamp', '<=', $request->data_fim);
        }

        $logs = $query->orderBy('timestamp', 'desc')->get();

        // Gerar CSV
        $filename = 'auditoria_' . Carbon::now()->format('Ymd_His') . '.csv';
        $handle = fopen('php://temp', 'w');

        // Cabeçalho
        fputcsv($handle, ['ID', 'Usuário', 'Ação', 'Entidade', 'Entidade ID', 'IP', 'Data/Hora', 'Dados Anteriores', 'Dados Novos']);

        // Dados
        foreach ($logs as $log) {
            fputcsv($handle, [
                $log->id,
                $log->usuario->name ?? 'N/A',
                $log->acao,
                $log->entidade,
                $log->entidade_id,
                $log->ip_address,
                $log->timestamp,
                json_encode($log->dados_anteriores, JSON_UNESCAPED_UNICODE),
                json_encode($log->dados_novos, JSON_UNESCAPED_UNICODE),
            ]);
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Limpar logs antigos (RN13 - retenção de 5 anos)
     */
    public function limpar(Request $request)
    {
        $request->validate([
            'anos_retencao' => 'required|integer|min:1|max:10'
        ]);

        $dataLimite = Carbon::now()->subYears($request->anos_retencao);
        $quantidade = LogAuditoria::where('timestamp', '<', $dataLimite)->count();

        if ($quantidade > 0) {
            LogAuditoria::where('timestamp', '<', $dataLimite)->delete();

            LogAuditoria::registrar(
                Auth::id(),
                'LIMPAR_LOGS',
                'log_auditoria',
                null,
                null,
                ['anos_retencao' => $request->anos_retencao, 'quantidade' => $quantidade]
            );

            return back()->with('success', "{$quantidade} logs removidos com sucesso!");
        }

        return back()->with('info', 'Não há logs antigos para remover.');
    }
}
