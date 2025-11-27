<?php

namespace App\Services;

use App\Models\Alliance;
use Illuminate\Support\Collection;

class AllianceService
{
    /**
     * Obtener alianzas activas por categoría con paginación simple.
     *
     * @param  int|null  $categoryId
     * @param  int  $page
     * @param  int  $perPage
     * @return array{data: Collection, total: int}
     */
    public function listByCategory(?int $categoryId, int $page = 1, int $perPage = 6): array
    {
        $query = Alliance::actives()
            ->byCategory($categoryId)
            ->with('customerCategories')
            ->orderBy('fecha_registro', 'desc');

        $total = $query->count();

        $items = $query
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return [
            'data' => $items,
            'total' => $total,
        ];
    }

    /**
     * Obtener alianza por id (nullable).
     *
     * @param  int  $id
     * @return Alliance|null
     */
    public function getById(int $id): ?Alliance
    {
        return Alliance::with('customerCategories')->find($id);
    }

    /**
     * Marcar alianza como usada.
     *
     * @param  Alliance  $alliance
     * @return Alliance
     */
    public function markAsUsed(Alliance $alliance): Alliance
    {
        // Asumo que el modelo Alliance tiene un método markAsUsed() que setea fecha_uso y estatus.
        if (method_exists($alliance, 'markAsUsed')) {
            $alliance->markAsUsed();
            $alliance->save();
        } else {
            // Fallback: marcar manualmente (ajusta campos a tu esquema)
            $alliance->estatus = 'usada';
            $alliance->fecha_uso = now();
            $alliance->save();
        }

        return $alliance->refresh();
    }
}
