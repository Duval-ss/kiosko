<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolMiddleware
{
    /**
     * Comprueba si el usuario posee uno de los roles permitidos.
     */
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response|JsonResponse {
        $usuario = $request->user();

        if (!$usuario) {
            return response()->json([
                'success' => false,

                'message' =>
                    'No has iniciado sesión.',

                'data' => null,
            ], 401);
        }

        if (!in_array($usuario->rol, $roles, true)) {
            return response()->json([
                'success' => false,

                'message' =>
                    'No tienes permiso para realizar esta acción.',

                'data' => null,
            ], 403);
        }

        return $next($request);
    }
}