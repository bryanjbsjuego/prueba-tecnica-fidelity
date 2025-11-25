<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthSessionMiddleware
{
    /**
     * Validar que existe una sesión válida en el request
     */
    public function handle(Request $request, Closure $next)
    {
        $session = $request->input('session') ?? $request->header('X-Session');

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Sesión requerida'
            ], 401);
        }

        return $next($request);
    }
}
