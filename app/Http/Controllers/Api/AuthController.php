<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginFormRequest;
use App\Services\SoapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(
        private SoapService $soapService,
    ) {}

    public function login(LoginFormRequest $request): JsonResponse
    {
        try {
            $email = $request->input('email');
            $password = $request->input('password');

            // Paso 1: SynchroAndLogin en Terminal
            $terminalResponse = $this->soapService->synchroAndLogin();

            if ($terminalResponse['answerCode'] != 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al conectar con el servicio de autenticación'
                ], 500);
            }

            $sessionID = $terminalResponse['sessionID'];
            $name = $terminalResponse['operator']['name'];
            $surname = $terminalResponse['operator']['surname'];

            // Paso 2: SearchCustomerByPersonalData
            $customerSearch = $this->soapService->searchCustomerByPersonalData($sessionID, $email, $name, $surname);

            if ($customerSearch['answerCode'] != 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'La cuenta no se encuentra registrada en nuestro sistema'
                ], 404);
            }

            // Paso 3: Validar status del cliente
            $customerList = $customerSearch['customerList'];

            if (!is_array($customerList)) {
                $customerList = [$customerList];
            }

            $validCustomer = null;
            foreach ($customerList as $customer) {
                if (
                    isset($customer['status']) && $customer['status'] == 1 &&
                    isset($customer['customer_area_status']) &&
                    in_array($customer['customer_area_status'], [1, 4])
                ) {
                    $validCustomer = $customer;
                    break;
                }
            }

            if (!$validCustomer) {
                return response()->json([
                    'success' => false,
                    'message' => 'La cuenta no se encuentra registrada en nuestro sistema'
                ], 403);
            }

            $userName = $validCustomer['personalInfo']['userName'] ?? null;

            if (!$userName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener información del usuario'
                ], 500);
            }

            // Paso 4: Synchro en Customer Area
            $caSync = $this->soapService->synchroCustomerArea();

            if ($caSync['answerCode'] != 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al conectar con el área de clientes'
                ], 500);
            }

            $caSession = $caSync['session'];

            // Paso 5: Login en Customer Area
            $caLogin = $this->soapService->loginCustomerArea($caSession, $userName, $password);

            if ($caLogin['answerCode'] != 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contraseña incorrecta'
                ], 401);
            }

            $customer = $caLogin['customer'];

            // Guardar sesión en storage/session si es necesario
            $sessionData = [
                'session' => $caSession,
                'customer' => $customer,
                'email' => $email,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Autenticación exitosa',
                'session' => $caSession,
                'customer' => $customer,
            ]);

        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error en el proceso de autenticación',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
