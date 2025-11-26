<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alliance;
use App\Models\SesionApi;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AllianceController extends Controller
{
    /**
     * Obtener alianzas activas por categoría de cliente
     */
    public function index(Request $request): JsonResponse
    {
        Log::info($request->all());
        $validator = Validator::make($request->all(), [
            'uuid' => 'required|string',
            'categoria_cliente_id' => 'required|integer|exists:categorias_cliente,categoria_cliente_id',
            'page' => 'integer|min:1',
            'limit' => 'integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Verificar que el UUID es válido y la sesión está activa
            $session = SesionApi::where('uuid', $request->uuid)
                ->actives()
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesión inválida o expirada'
                ], 401);
            }

            $customerIdCategory = $request->input('categoria_cliente_id');
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 6);

            // Obtener alianzas activas para la categoría
            $query = Alliance::actives()
                ->byCategory($customerIdCategory)
                ->with('customerCategories')
                ->orderBy('fecha_registro', 'desc');

            $total = $query->count();

            $alliences = $query
                ->skip(($page - 1) * $limit)
                ->take($limit)
                ->get();

            // Formatear respuesta
            $alliencesFormatted = $alliences->map(function($alliance) {
                return [
                    'id' => $alliance->id,
                    'nombre' => $alliance->nombre,
                    'descripcion' => $alliance->descripcion,
                    'estatus' => $alliance->estatus,
                    // 'vigencia_inicio' => $alliance->vigencia_inicio?->format('Y-m-d'),
                    // 'vigencia_fin' => $alliance->vigencia_fin?->format('Y-m-d'),
                    'fecha_registro' => $alliance->fecha_registro->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'alianzas' => $alliencesFormatted,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $total,
                    'total_pages' => ceil($total / $limit),
                ]
            ]);

        } catch (\Exception $e) {
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
    public function used(Request $request): JsonResponse
    {
        ddd($request->all());
        $validator = Validator::make($request->all(), [
            'uuid' => 'required|string',
            'alianza_id' => 'required|integer|exists:alianzas,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Verificar sesión activa
            $session = SesionApi::where('uuid', $request->uuid)
                ->actives()
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesión inválida o expirada'
                ], 401);
            }

            $allienceId = $request->input('alianza_id');

            // Buscar alianza
            $alliance = Alliance::find($allienceId);

            if (!$alliance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alianza no encontrada'
                ], 404);
            }

            // Verificar que está activa
            if (!$alliance->isActive()) {
                return response()->json([
                    'success' => false,
                    'message' => 'La alianza ya no está disponible'
                ], 400);
            }


            // Marcar como usada
            $alliance->markAsUsed();


            return response()->json([
                'success' => true,
                'message' => 'Alianza obtenida',
                'alianza' => [
                    'id' => $alliance->id,
                    'nombre' => $alliance->nombre,
                    'estatus' => $alliance->estatus,
                    'fecha_uso' => $alliance->fecha_uso->format('Y-m-d H:i:s'),
                ]
            ]);

        } catch (\Exception $e) {
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
            // Verificar sesión
            $uuid = $request->query('uuid');

            if (!$uuid) {
                return response()->json([
                    'success' => false,
                    'message' => 'UUID requerido'
                ], 400);
            }

            $session = SesionApi::where('uuid', $uuid)->actives()->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesión inválida o expirada'
                ], 401);
            }

            $alliance = Alliance::with('customerCategories')->find($id);

            if (!$alliance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alianza no encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'alianza' => [
                    'id' => $alliance->id,
                    'nombre' => $alliance->nombre,
                    'descripcion' => $alliance->descripcion,
                    'estatus' => $alliance->estatus,
                    // 'vigencia_inicio' => $alianza->vigencia_inicio?->format('Y-m-d'),
                    // 'vigencia_fin' => $alianza->vigencia_fin?->format('Y-m-d'),
                    'categorias' => $alliance->customerCategories->map(fn($cat) => [
                        'id' => $cat->categoria_cliente_id,
                        'nombre' => $cat->nombre,
                    ]),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get alianza detail error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el detalle de la alianza'
            ], 500);
        }
    }

}
