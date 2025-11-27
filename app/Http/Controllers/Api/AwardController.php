<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AwardRequest;
use App\Services\SoapService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AwardController extends Controller
{
    public function __construct(private SoapService $soapService) {}

    public function index(AwardRequest $request): JsonResponse
    {
        try {
            $session = $request->input('session');
            $page = (int) $request->input('page', 1);
            $limit = (int) $request->input('limit', 8);
            $search = $request->input('search', '');
            $sortBy = $request->input('sort_by', 'name');
            $order = $request->input('order', 'asc');

            Log::info('Award filter params', [
                'search' => $search,
                'sort_by' => $sortBy,
                'order' => $order,
                'page' => $page,
                'limit' => $limit
            ]);

            $response = $this->soapService->getPrizesByCustomer($session, 0, 100);

            if (!is_array($response) || ($response['answerCode'] ?? 1) != 0) {
                Log::error('GetPrizesByCustomer returned error', ['response' => $response]);
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener los premios'
                ], 500);
            }

            $rawPrizeList = $response['prizeList'] ?? [];

            // Normalizar a array
            if (!is_array($rawPrizeList)) {
                $rawPrizeList = empty($rawPrizeList) ? [] : [$rawPrizeList];
            }

            $raw_count = count($rawPrizeList);

            // Filtrar por búsqueda (nombre del premio)
            $filteredList = $rawPrizeList;
            if (!empty($search)) {
                $filteredList = array_filter($rawPrizeList, function($prize) use ($search) {
                    return stripos($prize['name'] ?? '', $search) !== false;
                });
                $filteredList = array_values($filteredList);
            }

            // Ordenar por nombre, puntos o dinero
            usort($filteredList, function($a, $b) use ($sortBy, $order) {
                if ($sortBy === 'money') {
                    // Limpiar y convertir a float, manejando posibles formatos como "1,234.56"
                    $valA = floatval(str_replace(',', '', $a['moneyValue'] ?? 0));
                    $valB = floatval(str_replace(',', '', $b['moneyValue'] ?? 0));
                    $cmp = $valA <=> $valB;
                } elseif ($sortBy === 'points') {
                    $valA = intval($a['points'] ?? 0);
                    $valB = intval($b['points'] ?? 0);
                    $cmp = $valA <=> $valB;
                } else {
                    // Ordenar por nombre (alfabéticamente)
                    $valA = strtolower($a['name'] ?? '');
                    $valB = strtolower($b['name'] ?? '');
                    $cmp = strcmp($valA, $valB);
                }

                return $order === 'desc' ? -$cmp : $cmp;
            });

            $filtered_count = count($filteredList);
            $total_pages = (int) ceil($filtered_count / $limit);

            // Validar página actual
            if ($page > $total_pages && $total_pages > 0) {
                $page = $total_pages;
            }
            if ($page < 1) {
                $page = 1;
            }

            // Paginar localmente
            $offset = ($page - 1) * $limit;
            $pagedList = array_slice($filteredList, $offset, $limit);
            $paged_count = count($pagedList);

            // Formatear respuesta
            $prizes = array_map(function($prize) {
                return [
                    'id' => $prize['id'] ?? null,
                    'name' => $prize['name'] ?? '',
                    'points' => $prize['points'] ?? 0,
                    'image' => $prize['pathImageAbsolute'] ?? '',
                    'moneyValue' => $prize['moneyValue'] ?? 0,
                    'description' => $prize['description'] ?? '',
                ];
            }, $pagedList);

            return response()->json([
                'success' => true,
                'prizes' => $prizes,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $filtered_count,
                    'total_pages' => $total_pages,
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