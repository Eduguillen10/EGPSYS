<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class TwoFactorController extends Controller
{
    private const CODE_TTL_SECONDS = 30;

    public function show(Request $request)
    {
        if (! $request->session()->has('two_factor.idusuario')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ], [
            'code.required' => 'Ingrese el codigo de verificacion.',
            'code.digits' => 'El codigo de verificacion debe tener 6 digitos.',
        ]);

        $user = $this->pendingUser($request);

        if (! $user) {
            return redirect()->route('login');
        }

        $cacheKey = $this->cacheKey($user->id);
        $codeHash = Cache::get($cacheKey);

        if (! $codeHash) {
            return back()
                ->withErrors(['code' => 'El codigo vencio. Solicite un nuevo codigo.'])
                ->withInput();
        }

        if (! Hash::check($request->input('code'), $codeHash)) {
            $this->recordTwoFactorAttempt($request, $user, false, 'Codigo 2FA invalido');

            return back()
                ->withErrors(['code' => 'El codigo de verificacion no es correcto.'])
                ->withInput();
        }

        Cache::forget($cacheKey);

        $remember = (bool) $request->session()->pull('two_factor.remember', false);
        $request->session()->forget('two_factor');
        $request->session()->regenerate();

        Auth::login($user, $remember);

        $request->session()->put('auth.password_confirmed_at', time());
        $this->recordTwoFactorAttempt($request, $user, true, 'Acceso correcto con 2FA');

        return redirect()->intended('/seleccionsucursales/seleccionar');
    }

    public function resend(Request $request)
    {
        $user = $this->pendingUser($request);

        if (! $user) {
            return redirect()->route('login');
        }

        $this->sendCode($user);

        return back()->with('status', 'Se envio un nuevo codigo. Recuerde que vence en 30 segundos.');
    }

    public static function sendCode(User $user): void
    {
        $code = (string) random_int(100000, 999999);

        Cache::put(
            self::cacheKey($user->id),
            Hash::make($code),
            now()->addSeconds(self::CODE_TTL_SECONDS)
        );

        $body = implode(PHP_EOL, [
            'Codigo de verificacion para ingresar a SYSEGP:',
            '',
            $code,
            '',
            'Este codigo vence en 30 segundos.',
            'Si usted no intento ingresar, ignore este mensaje.',
        ]);

        Mail::raw($body, function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Codigo de verificacion 2FA - SYSEGP');
        });
    }

    private function pendingUser(Request $request): ?User
    {
        $userId = $request->session()->get('two_factor.idusuario');

        if (! $userId) {
            return null;
        }

        return User::find($userId);
    }

    private static function cacheKey(int $userId): string
    {
        return 'two_factor:code:' . $userId;
    }

    private function recordTwoFactorAttempt(Request $request, User $user, bool $successful, string $status): void
    {
        try {
            LoginAttempt::create([
                'idusuario' => $user->id,
                'email' => $user->email,
                'password_mask' => null,
                'successful' => $successful,
                'status' => $status,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'attempted_at' => now(),
            ]);
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
