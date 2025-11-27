<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MarkAsUsedRequest;
use App\Http\Requests\AllianceRequest;
use App\Http\Resources\AllianceResource;
use App\Services\AllianceService;
use App\Services\SessionService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AllianceController extends Controller
{
    private SessionService $sessionService;
    private AllianceService $allianceService;

    public function __construct(SessionService $sessionService, AllianceService $allianceService)
    {
        $this->sessionService = $sessionService;
        $this->allianceService = $allianceService;
    }

    /**
     * Obtener alianzas activas por categoría de cliente
     */
    public function index(AllianceRequest $request): JsonResponse
    {
        try {
            $session = $this->sessionService->validateUuid($request->uuid);

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesión inválida o expirada'
                ], 401);
            }

            $customerIdCategory = $request->input('categoria_cliente_id');
            $page = (int)$request->input('page', 1);
            $limit = (int)$request->input('limit', 6);

            $result = $this->allianceService->listByCategory($customerIdCategory, $page, $limit);
            $alliences = $result['data'];
            $total = $result['total'];

            $formatted = AllianceResource::collection($alliences);

            return response()->json([
                'success' => true,
                'alianzas' => $formatted,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $total,
                    'total_pages' => (int)ceil($total / max($limit, 1)),
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Get alianzas error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las alianzas',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Marcar alianza como usada
     */
    public function used(MarkAsUsedRequest $request): JsonResponse
    {
        try {
            $session = $this->sessionService->validateUuid($request->input('uuid'));

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesión inválida o expirada'
                ], 401);
            }

            $allienceId = (int)$request->input('alianza_id');

            $alliance = $this->allianceService->getById($allienceId);

            if (!$alliance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alianza no encontrada'
                ], 404);
            }

            if (method_exists($alliance, 'isActive') && !$alliance->isActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'La alianza ya no está disponible'
                ], 400);
            }

            $alliance = $this->allianceService->markAsUsed($alliance);

            return response()->json([
                'success' => true,
                'message' => 'Alianza obtenida',
                'alianza' => new AllianceResource($alliance)
            ]);
        } catch (Exception $e) {
            Log::error('Marcar alianza usada error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al marcar la alianza como usada',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Obtener detalle de una alianza
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $uuid = $request->query('uuid');

            $session = $this->sessionService->validateUuid($uuid);

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesión inválida o expirada'
                ], 401);
            }

            $alliance = $this->allianceService->getById($id);

            if (!$alliance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alianza no encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'alianza' => new AllianceResource($alliance)
            ]);
        } catch (Exception $e) {
            Log::error('Get alianza detail error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el detalle de la alianza'
            ], 500);
        }
    }
}
