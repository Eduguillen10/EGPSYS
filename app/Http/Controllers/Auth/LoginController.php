<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginAttempt;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers {
        sendFailedLoginResponse as protected baseSendFailedLoginResponse;
        sendLockoutResponse as protected baseSendLockoutResponse;
    }

    protected $maxAttempts = 3;

    protected $decayMinutes = 1;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/seleccionsucursales/seleccionar';

    public function login(Request $request)
    {
        $this->validateLogin($request);

        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if (Auth::validate($this->credentials($request))) {
            $user = User::where($this->username(), $request->input($this->username()))->first();

            if (! $user) {
                $this->incrementLoginAttempts($request);

                return $this->sendFailedLoginResponse($request);
            }

            $this->clearLoginAttempts($request);
            $this->recordLoginAttempt($request, false, 'Credenciales validas, 2FA pendiente', $user->id);

            try {
                TwoFactorController::sendCode($user);
            } catch (\Throwable $exception) {
                report($exception);

                throw ValidationException::withMessages([
                    $this->username() => ['No se pudo enviar el codigo de verificacion. Intente nuevamente.'],
                ]);
            }

            $request->session()->put('two_factor', [
                'idusuario' => $user->id,
                'remember' => $request->boolean('remember'),
            ]);

            return redirect()->route('two-factor.show')
                ->with('status', 'Se envio un codigo de verificacion a su correo. Vence en 30 segundos.');
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    protected function authenticated(Request $request, $user)
    {
        $this->recordLoginAttempt($request, true, 'Acceso correcto', $user->id);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $this->recordLoginAttempt($request, false, 'Credenciales invalidas');

        return $this->baseSendFailedLoginResponse($request);
    }

    protected function sendLockoutResponse(Request $request)
    {
        $this->recordLoginAttempt($request, false, 'Bloqueado por intentos fallidos');
        $this->sendLockoutAlert($request);

        return $this->baseSendLockoutResponse($request);
    }

    private function recordLoginAttempt(Request $request, bool $successful, string $status, ?int $userId = null): void
    {
        try {
            $email = $request->input($this->username());

            LoginAttempt::create([
                'idusuario' => $userId ?: User::where('email', $email)->value('id'),
                'email' => $email,
                'password_mask' => $this->maskPassword($request->input('password')),
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

    private function maskPassword(?string $password): ?string
    {
        if ($password === null || $password === '') {
            return null;
        }

        return str_repeat('*', min(strlen($password), 20));
    }

    private function sendLockoutAlert(Request $request): void
    {
        try {
            $to = config('security.alert_email');

            if (empty($to)) {
                return;
            }

            $email = (string) $request->input($this->username());
            $ipAddress = $request->ip() ?: 'No disponible';
            $cacheKey = 'security:lockout-alert:' . sha1(strtolower($email) . '|' . $ipAddress);

            if (! Cache::add($cacheKey, true, now()->addMinutes($this->decayMinutes))) {
                return;
            }

            $body = implode(PHP_EOL, [
                'Se detecto un bloqueo temporal por intentos fallidos de acceso.',
                '',
                'Correo: ' . ($email ?: 'No disponible'),
                'IP: ' . $ipAddress,
                'Fecha y hora: ' . now()->format('d/m/Y H:i:s'),
                'Navegador: ' . ($request->userAgent() ?: 'No disponible'),
                '',
                'Revise el modulo Acceso > Intentos de acceso para mas detalle.',
            ]);

            Mail::raw($body, function ($message) use ($to) {
                $message->to($to)
                    ->subject('Alerta de acceso bloqueado - SYSEGP');
            });
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
