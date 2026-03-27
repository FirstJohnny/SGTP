<?php
// app/Observers/VeiculoObserver.php
namespace App\Observers;

use App\Models\Veiculo;
use App\Models\LogAuditoria;
use Illuminate\Support\Facades\Auth;

class VeiculoObserver
{
    public function created(Veiculo $veiculo)
    {
        LogAuditoria::registrar(
            Auth::id(),
            'CREATE',
            'veiculo',
            $veiculo->id,
            null,
            $veiculo->toArray()
        );
    }

    public function updated(Veiculo $veiculo)
    {
        LogAuditoria::registrar(
            Auth::id(),
            'UPDATE',
            'veiculo',
            $veiculo->id,
            $veiculo->getOriginal(),
            $veiculo->getChanges()
        );
    }

    public function deleted(Veiculo $veiculo)
    {
        LogAuditoria::registrar(
            Auth::id(),
            'DELETE',
            'veiculo',
            $veiculo->id,
            $veiculo->toArray(),
            null
        );
    }
}
