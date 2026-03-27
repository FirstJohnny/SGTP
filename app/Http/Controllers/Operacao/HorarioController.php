<?php

namespace App\Http\Controllers\Operacao;

use App\Http\Controllers\Controller;
use App\Models\Rota;
use App\Models\Horario;
use App\Models\LogAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HorarioController extends Controller
{
    /**
     * Listar horários de uma rota
     */
    public function index(Request $request)
    {
        $rotaId = $request->get('rota') ?? $request->rota;
        $rota = Rota::findOrFail($rotaId);
        $horarios = $rota->horarios()->orderBy('hora_partida')->paginate(15);
        return view('operacao.horarios.index', compact('rota', 'horarios'));
    }

    /**
     * Formulário para adicionar horário
     */
    public function create(Request $request)
    {
        $rotaId = $request->get('rota') ?? $request->rota;
        $rota = Rota::findOrFail($rotaId);
        return view('operacao.horarios.create', compact('rota'));
    }

    /**
     * Adicionar horário
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rota_id' => 'required|exists:rota,id',
            'hora_partida' => 'required|date_format:H:i',
            'hora_chegada' => 'required|date_format:H:i|after:hora_partida',
            'dias_semana' => 'required|string|max:50',
            'tipo_dia' => 'required|in:NORMAL,FERIADO,ESPECIAL',
            'ativo' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            $validated['ativo'] = $request->boolean('ativo');
            $horario = Horario::create($validated);

            LogAuditoria::registrar(
                Auth::id(),
                'CREATE',
                'horario',
                $horario->id,
                null,
                $horario->toArray()
            );

            DB::commit();

            return redirect()->route('horarios.index', ['rota' => $validated['rota_id']])
                ->with('success', 'Horário adicionado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao adicionar horário: ' . $e->getMessage());
        }
    }

    /**
     * Editar horário
     */
    public function edit(Horario $horario)
    {
        $rota = $horario->rota;
        return view('operacao.horarios.edit', compact('horario', 'rota'));
    }

    /**
     * Atualizar horário
     */
    public function update(Request $request, Horario $horario)
    {
        $validated = $request->validate([
            'hora_partida' => 'required|date_format:H:i',
            'hora_chegada' => 'required|date_format:H:i|after:hora_partida',
            'dias_semana' => 'required|string|max:50',
            'tipo_dia' => 'required|in:NORMAL,FERIADO,ESPECIAL',
            'ativo' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            $dadosAntigos = $horario->toArray();
            $validated['ativo'] = $request->boolean('ativo');
            $horario->update($validated);

            LogAuditoria::registrar(
                Auth::id(),
                'UPDATE',
                'horario',
                $horario->id,
                $dadosAntigos,
                $horario->toArray()
            );

            DB::commit();

            return redirect()->route('horarios.index', ['rota' => $horario->rota_id])
                ->with('success', 'Horário atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao atualizar horário: ' . $e->getMessage());
        }
    }

    /**
     * Remover horário
     */
    public function destroy(Horario $horario)
    {
        DB::beginTransaction();
        try {
            $rotaId = $horario->rota_id;
            $dados = $horario->toArray();
            $horario->delete();

            LogAuditoria::registrar(
                Auth::id(),
                'DELETE',
                'horario',
                $horario->id,
                $dados,
                null
            );

            DB::commit();

            return redirect()->route('horarios.index', ['rota' => $rotaId])
                ->with('success', 'Horário removido com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao remover horário: ' . $e->getMessage());
        }
    }

    /**
     * API: Horários por rota para consulta pública
     */
    public function publicSchedules(Request $request)
    {
        $validated = $request->validate([
            'rota_id' => 'required|exists:rota,id',
            'data' => 'nullable|date'
        ]);

        $data = $validated['data'] ? Carbon::parse($validated['data']) : Carbon::today();
        $diaSemana = strtoupper(substr($data->format('D'), 0, 3));
        $tipoDia = $data->isHoliday() ? Horario::TIPO_FERIADO : Horario::TIPO_NORMAL;

        $horarios = Horario::where('rota_id', $validated['rota_id'])
            ->where('ativo', true)
            ->where(function($q) use ($diaSemana, $tipoDia) {
                $q->where('dias_semana', 'like', "%{$diaSemana}%")
                  ->orWhere('tipo_dia', $tipoDia);
            })
            ->orderBy('hora_partida')
            ->get();

        return response()->json($horarios);
    }
}