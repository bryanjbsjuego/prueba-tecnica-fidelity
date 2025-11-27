<?php

namespace App\Services;

use App\Models\SesionApi;

class SessionService
{
    /**
     * Valida el UUID y devuelve la sesión activa o null si no existe/está expirada.
     *
     * @param  string|null  $uuid
     * @return SesionApi|null
     */
    public function validateUuid(?string $uuid): ?SesionApi
    {
        if (empty($uuid)) {
            return null;
        }

        return SesionApi::where('uuid', $uuid)
            ->actives()
            ->first();
    }
}
