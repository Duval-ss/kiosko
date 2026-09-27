<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Inicia sesión y genera un token.
     */
    public function login(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
            ],

            'password' => [
                'required',
                'string',
            ],

            'dispositivo' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        $usuario = User::where(
            'email',
            $datos['email']
        )->first();

        if (
            !$usuario
            || !Hash::check(
                $datos['password'],
                $usuario->password
            )
        ) {
            return response()->json([
                'success' => false,

                'message' =>
                    'El correo o la contraseña son incorrectos.',

                'data' => null,
            ], 401);
        }

        $nombreToken =
            $datos['dispositivo']
            ?? 'angular-kiosko-junior';

        $token = $usuario
            ->createToken($nombreToken)
            ->plainTextToken;

        return response()->json([
            'success' => true,

            'message' =>
                'Inicio de sesión correcto.',

            'data' => [
                'usuario' =>
                    $this->datosUsuario($usuario),

                'token' =>
                    $token,

                'tipo_token' =>
                    'Bearer',
            ],
        ]);
    }

    /**
     * Devuelve el usuario autenticado.
     */
    public function usuario(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,

            'message' =>
                'Usuario autenticado consultado correctamente.',

            'data' => [
                'usuario' =>
                    $this->datosUsuario(
                        $request->user()
                    ),
            ],
        ]);
    }

    /**
     * Cierra la sesión actual.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()
            ->currentAccessToken()
            ?->delete();

        return response()->json([
            'success' => true,

            'message' =>
                'Sesión cerrada correctamente.',

            'data' => null,
        ]);
    }

    /**
     * Prepara los datos públicos del usuario.
     */
    private function datosUsuario(User $usuario): array
    {
        return [
            'id' =>
                $usuario->id,

            'nombre' =>
                $usuario->name,

            'email' =>
                $usuario->email,

            'rol' =>
                $usuario->rol,
        ];
    }
}