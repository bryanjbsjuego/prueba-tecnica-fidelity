<?php

namespace App\Services;

use App\Models\Operator;
use App\Models\SesionApi;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

class OperatorAuthService
{
    /**
     * Intenta autenticar un operador, generar token y crear sesión.
     *
     * @param  string  $usuario
     * @param  string  $contrasena
     * @param  int  $sessionMinutes
     * @return array{operator: Operator, token: string, session: SesionApi}
     *
     * @throws Exception on invalid credentials
     */
    public function attempt(string $usuario, string $contrasena, int $sessionMinutes = 60): array
    {
        $operator = Operator::where('usuario', $usuario)
            ->actives()
            ->with('program')
            ->first();

        if (!$operator || !$operator->verifyPassword($contrasena)) {
            throw new Exception('Credenciales inválidas', 401);
        }

        $token = JWTAuth::fromUser($operator);

        // Asumo que SesionApi::createSession existe y tiene la misma firma
        $sesion = SesionApi::createSession($operator, $token, $sessionMinutes);

        return [
            'operator' => $operator,
            'token' => $token,
            'session' => $sesion,
        ];
    }

    /**
     * Cierra/s invalida la sesión y el token JWT si se proveen.
     *
     * @param  string|null  $uuid
     * @param  string|null  $token
     * @return void
     */
    public function logout(?string $uuid, ?string $token): void
    {
        if ($uuid) {
            $sesion = SesionApi::where('uuid', $uuid)->first();
            $sesion?->invalidate();
        }

        // Invalidar token JWT si se proporciona
        try {
            if ($token) {
                JWTAuth::setToken($token);
                JWTAuth::invalidate();
            } else {
                $current = JWTAuth::getToken();
                if ($current) {
                    JWTAuth::invalidate($current);
                }
            }
        } catch (\Throwable $e) {

        }
    }
}
