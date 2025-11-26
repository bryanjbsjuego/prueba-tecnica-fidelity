<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SoapService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AwardController extends Controller
{
    public function __construct(private SoapService $soapService) {}

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'session' => 'required|string',
            'page' => 'integer|min:1',
            'limit' => 'integer|min:1|max:100',
            'search' => 'string|nullable',
            'order' => 'in:asc,desc|nullable',
        ]);

        try {
            $session = $request->input('session');
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 8);
            $search = $request->input('search', '');
            $order = $request->input('order', 'asc');

            $initLimit = ($page - 1) * $limit;

            // Llamada al WS para obtener premios
            $response = $this->soapService->getPrizesByCustomer($session, $initLimit, 100);

            if ($response['answerCode'] != 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener los premios'
                ], 500);
            }

            $prizeList = $response['prizeList'] ?? [];

            if (!is_array($prizeList)) {
                $prizeList = [$prizeList];
            }

            // Filtrar por búsqueda
            if (!empty($search)) {
                $prizeList = array_filter($prizeList, function($prize) use ($search) {
                    return stripos($prize['name'] ?? '', $search) !== false;
                });
                $prizeList = array_values($prizeList); // Reindexar
            }

            // Ordenar por nombre
            usort($prizeList, function($a, $b) use ($order) {
                $nameA = $a['name'] ?? '';
                $nameB = $b['name'] ?? '';

                if ($order === 'desc') {
                    return strcmp($nameB, $nameA);
                }
                return strcmp($nameA, $nameB);
            });

            $total = count($prizeList);

            // Paginar
            $prizeList = array_slice($prizeList, $initLimit, $limit);

            // Formatear respuesta
            $prizes = array_map(function($prize) {
                return [
                    'id' => $prize['id'] ?? null,
                    'name' => $prize['name'] ?? '',
                    'points' => $prize['points'] ?? 0,
                    'image' => $prize['pathImageAbsolute'] ?? '',
                    'description' => $prize['description'] ?? '',
                ];
            }, $prizeList);

            Log::info($prizes);

            return response()->json([
                'success' => true,
                'prizes' => $prizes,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $total,
                    'total_pages' => ceil($total / $limit),
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Get prizes error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los premios',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
