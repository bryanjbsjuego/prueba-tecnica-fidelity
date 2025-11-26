<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginFormRequestApi;
use App\Models\Operator;
use App\Models\SesionApi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class OperatorAuthController extends Controller
{
    public function login(LoginFormRequestApi $request): JsonResponse
    {
        try {
            $operator = Operator::where('usuario', $request->usuario)
                ->actives()
                ->with('program')
                ->first();

            if (!$operator) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales inválidas'
                ], 401);
            }

            if (!$operator->verifyPassword($request->contrasena)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales inválidas'
                ], 401);
            }

            // Generar token JWT
            $token = JWTAuth::fromUser($operator);

            // Crear sesión
            $sesion = SesionApi::createSession($operator, $token, 60);

            return response()->json([
                'success' => true,
                'message' => 'Autenticación exitosa',
                'uuid' => $sesion->uuid,
                'token' => $token,
                'operador' => [
                    'id' => $operator->operador_id,
                    'usuario' => $operator->usuario,
                    'programa' => $operator->program->programa_id,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Operator login error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error en el proceso de autenticación'
            ], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $uuid = $request->input('uuid');

            if ($uuid) {
                $sesion = SesionApi::where('uuid', $uuid)->first();
                $sesion?->invalidate();
            }

            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'success' => true,
                'message' => 'Sesión cerrada correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar sesión'
            ], 500);
        }
    }
}
