<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginFormRequest;
use App\Services\SoapService;
use Exception;
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
            $synchroResponse  = $this->soapService->synchroAndLogin();

            if (!isset($synchroResponse['answerCode']) || $synchroResponse['answerCode'] != 0) {
                Log::error('Error en SynchroAndLogin', $synchroResponse);
                return response()->json([
                    'success' => false,
                    'message' => 'Error al conectar con el servicio'
                ], 500);
            }


            $sessionID = $synchroResponse['sessionID'];
            $name = $synchroResponse['operator']['name'];
            $surname = $synchroResponse['operator']['surname'];

            // Paso 2: SearchCustomerByPersonalData
            $customerSearch = $this->soapService->searchCustomerByPersonalData($sessionID, $email, $name, $surname);


            if (!isset($customerSearch['answerCode']) || $customerSearch['answerCode'] != 0) {
                Log::error('Error en SearchCustomerByPersonalData', $customerSearch);
                return response()->json([
                    'success' => false,
                    'message' => 'Error al buscar el cliente'
                ], 500);
            }

            if (empty($customerSearch['customerList'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'La cuenta no se encuentra registrada en nuestro sistema'
                ], 404);
            }

            // Paso 3: Validar status del cliente
            $customerList = $customerSearch['customerList'];

            if (!isset($customerList[0])) {
                $customerList = [$customerList];
            }

            $validCustomer = null;
            foreach ($customerList as $customer) {

                // Validación según requerimientos:
                // status debe ser 1
                // customer_area_status debe ser 1 o 4
                if (
                    isset($customer['status']) &&
                    $customer['status'] == 1 &&
                    isset($customer['customer_area_status']) &&
                    in_array($customer['customer_area_status'], [1, 4])
                ) {
                    $validCustomer = $customer;
                    Log::info('Cliente válido encontrado', [
                        'id' => $customer['id'] ?? 'N/A',
                        'userName' => $customer['personalInfo']['userName'] ?? 'N/A'
                    ]);
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

            return response()->json([
                'success' => true,
                'message' => 'Autenticación exitosa',
                'session' => $caSession,
                'customer' => $customer,
                'userName' => $userName
            ]);
        } catch (Exception $e) {
            Log::error('Login error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error en el proceso de autenticación',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
