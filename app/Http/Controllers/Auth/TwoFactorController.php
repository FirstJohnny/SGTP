<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LogAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;
use App\Mail\TwoFactorCodeMail;

class TwoFactorController extends Controller
{
    protected $google2fa;


    /**
     * Mostrar formulário de verificação 2FA
     */
    public function showVerifyForm()
    {
        $user = Auth::user();

        // Se 2FA não estiver habilitado, redirecionar para dashboard
        if (!$user->two_factor_enabled) {
            return redirect()->route('dashboard');
        }

        // Se já verificou na sessão, redirecionar
        if (session('2fa_verified')) {
            return redirect()->route('dashboard');
        }

        return view('auth.2fa.verify');
    }

    /**
     * Verificar código 2FA
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $user = Auth::user();

        if (!$user->two_factor_enabled) {
            return redirect()->route('dashboard');
        }

        // Verificar código
        $valid = $this->google2fa->verifyKey(
            $user->two_factor_secret,
            $request->code,
            2 // 2 posições de desvio
        );

        if ($valid) {
            session(['2fa_verified' => true]);

            LogAuditoria::registrar(
                $user->id,
                '2FA_VERIFY_SUCCESS',
                'users',
                $user->id,
                null,
                ['ip' => $request->ip()]
            );

            return redirect()->intended('dashboard');
        }

        LogAuditoria::registrar(
            $user->id,
            '2FA_VERIFY_FAILED',
            'users',
            $user->id,
            null,
            ['ip' => $request->ip(), 'code_attempt' => $request->code]
        );

        return back()->withErrors(['code' => 'Código inválido. Tente novamente.']);
    }

    /**
     * Habilitar 2FA para o usuário
     */
    public function enable(Request $request)
    {
        $user = Auth::user();

        if ($user->two_factor_enabled) {
            return back()->with('error', '2FA já está habilitado.');
        }

        // Gerar segredo
        $secret = $this->google2fa->generateSecretKey();

        // Gerar URL para QR Code
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        // Armazenar na sessão para confirmação
        session(['2fa_secret_temp' => $secret]);

        return view('auth.2fa.enable', [
            'qrCodeUrl' => $qrCodeUrl,
            'secret' => $secret
        ]);
    }

    /**
     * Confirmar ativação do 2FA
     */
    public function confirmEnable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $user = Auth::user();
        $secret = session('2fa_secret_temp');

        if (!$secret) {
            return back()->with('error', 'Sessão expirada. Tente novamente.');
        }

        $valid = $this->google2fa->verifyKey($secret, $request->code);

        if ($valid) {
            $user->two_factor_enabled = true;
            $user->two_factor_secret = $secret;
            $user->save();

            session()->forget('2fa_secret_temp');

            // Gerar códigos de backup
            $backupCodes = $this->generateBackupCodes($user);

            LogAuditoria::registrar(
                $user->id,
                '2FA_ENABLED',
                'users',
                $user->id,
                null,
                ['ip' => $request->ip()]
            );

            return view('auth.2fa.backup-codes', compact('backupCodes'));
        }

        return back()->withErrors(['code' => 'Código inválido. Tente novamente.']);
    }

    /**
     * Desabilitar 2FA
     */
    public function disable(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'password' => 'required|current_password'
        ]);

        $user->two_factor_enabled = false;
        $user->two_factor_secret = null;
        $user->save();

        // Limpar códigos de backup
        Cache::forget('2fa_backup_codes_' . $user->id);

        LogAuditoria::registrar(
            $user->id,
            '2FA_DISABLED',
            'users',
            $user->id,
            null,
            ['ip' => $request->ip()]
        );

        return redirect()->route('profile')
            ->with('success', '2FA desabilitado com sucesso.');
    }

    /**
     * Reenviar código de verificação (via e-mail/SMS)
     */
    public function resend(Request $request)
    {
        $user = Auth::user();

        // Verificar limite de reenvio
        $key = '2fa_resend_' . $user->id;
        if (Cache::has($key)) {
            return back()->with('error', 'Aguarde 60 segundos antes de reenviar.');
        }

        // Gerar código temporário
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Cache::put('2fa_code_' . $user->id, $code, 300); // 5 minutos de validade

        // Enviar por e-mail
        Mail::to($user->email)->send(new TwoFactorCodeMail($code));

        Cache::put($key, true, 60); // Limitar a 1 envio por minuto

        LogAuditoria::registrar(
            $user->id,
            '2FA_CODE_RESEND',
            'users',
            $user->id,
            null,
            ['ip' => $request->ip()]
        );

        return back()->with('success', 'Código reenviado para seu e-mail.');
    }
    /**
     * Gerar códigos de backup para 2FA
     */
    private function generateBackupCodes($user)
    {
        $codes = [];

        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(Str::random(10));
        }

        Cache::forever('2fa_backup_codes_' . $user->id, $codes);

        return $codes;
    }

    /**
     * Verificar código de backup
     */
    public function verifyBackup(Request $request)
    {
        $request->validate([
            'backup_code' => 'required|string'
        ]);

        $user = Auth::user();
        $backupCodes = Cache::get('2fa_backup_codes_' . $user->id, []);

        $index = array_search($request->backup_code, $backupCodes);

        if ($index !== false) {
            // Remover código usado
            unset($backupCodes[$index]);
            Cache::forever('2fa_backup_codes_' . $user->id, array_values($backupCodes));

            session(['2fa_verified' => true]);

            LogAuditoria::registrar(
                $user->id,
                '2FA_BACKUP_USED',
                'users',
                $user->id,
                null,
                ['ip' => $request->ip()]
            );

            return redirect()->intended('dashboard');
        }

        return back()->withErrors(['backup_code' => 'Código de backup inválido.']);
    }
}
