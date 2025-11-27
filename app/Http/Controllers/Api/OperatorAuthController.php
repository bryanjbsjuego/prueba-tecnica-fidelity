<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginFormRequestApi;
use App\Http\Resources\OperatorResource;
use App\Services\OperatorAuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

class OperatorAuthController extends Controller
{
    private OperatorAuthService $authService;

    public function __construct(OperatorAuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginFormRequestApi $request): JsonResponse
    {
        try {
            $result = $this->authService->attempt($request->usuario, $request->contrasena);

            return response()->json([
                'success' => true,
                'message' => 'Autenticación exitosa',
                'uuid' => $result['session']->uuid,
                'token' => $result['token'],
                'operador' => new OperatorResource($result['operator']),
            ]);
        } catch (Exception $e) {
            Log::error('Operator login error: ' . $e->getMessage());

            $status = $e->getCode() === 401 ? 401 : 500;
            $message = $e->getCode() === 401 ? 'Credenciales inválidas' : 'Error en el proceso de autenticación';

            return response()->json([
                'success' => false,
                'message' => $message
            ], $status);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $uuid = $request->input('uuid');

            $tokenObj = JWTAuth::getToken();
            $tokenString = $tokenObj ? (string)$tokenObj : null;

            $this->authService->logout($uuid, $tokenString);

            return response()->json([
                'success' => true,
                'message' => 'Sesión cerrada correctamente'
            ]);
        } catch (Exception $e) {
            Log::error('Operator logout error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar sesión'
            ], 500);
        }
    }
}
