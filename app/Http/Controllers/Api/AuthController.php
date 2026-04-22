<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Registro de nuevo usuario.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cedula' => ['required', 'string', 'max:20', 'unique:users'],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $user = User::create([
            'cedula' => $validated['cedula'],
            'name' => $validated['name'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole('usuario');

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado exitosamente.',
            'user' => $user->load('roles:id,name'),
            'token' => $token,
        ], 201);
    }

    /**
     * Inicio de sesión.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'cedula' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $agentInfo = LoginLog::parseUserAgent($request->userAgent() ?? '');
        $geoInfo = LoginLog::getGeoLocation($request->ip());

        if (!Auth::attempt($request->only('cedula', 'password'))) {
            // Registrar intento fallido
            LoginLog::create([
                'user_id' => User::where('cedula', $request->cedula)->value('id'),
                'cedula' => $request->cedula,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'platform' => $agentInfo['platform'],
                'browser' => $agentInfo['browser'],
                'device_type' => $agentInfo['device_type'],
                'country' => $geoInfo['country'],
                'region' => $geoInfo['region'],
                'city' => $geoInfo['city'],
                'isp' => $geoInfo['isp'],
                'latitude' => $geoInfo['latitude'],
                'longitude' => $geoInfo['longitude'],
                'status' => 'failed',
                'failure_reason' => 'Credenciales incorrectas',
                'logged_in_at' => now(),
            ]);

            throw ValidationException::withMessages([
                'cedula' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        $user = User::where('cedula', $request->cedula)->firstOrFail();

        // Registrar login exitoso
        LoginLog::create([
            'user_id' => $user->id,
            'cedula' => $user->cedula,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'platform' => $agentInfo['platform'],
            'browser' => $agentInfo['browser'],
            'device_type' => $agentInfo['device_type'],
            'country' => $geoInfo['country'],
            'region' => $geoInfo['region'],
            'city' => $geoInfo['city'],
            'isp' => $geoInfo['isp'],
            'latitude' => $geoInfo['latitude'],
            'longitude' => $geoInfo['longitude'],
            'status' => 'success',
            'logged_in_at' => now(),
        ]);

        // Revocar tokens anteriores para seguridad
        $user->tokens()->delete();

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso.',
            'user' => $user->load('roles:id,name', 'permissions:id,name'),
            'token' => $token,
        ]);
    }

    /**
     * Cerrar sesión (revocar token actual).
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente.',
        ]);
    }

    /**
     * Cerrar todas las sesiones (revocar todos los tokens).
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Todas las sesiones han sido cerradas.',
        ]);
    }

    /**
     * Obtener usuario autenticado con roles y permisos.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user()->load('roles:id,name', 'permissions:id,name'),
        ]);
    }

    /**
     * Cambiar contraseña (usuario autenticado).
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        // Revocar otros tokens, mantener el actual
        $request->user()->tokens()->where('id', '!=', $request->user()->currentAccessToken()->id)->delete();

        return response()->json([
            'message' => 'Contraseña actualizada exitosamente.',
        ]);
    }

    /**
     * Restablecer contraseña por cédula (admin).
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'cedula' => ['required', 'string', 'exists:users,cedula'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $user = User::where('cedula', $request->cedula)->firstOrFail();

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        // Revocar todos los tokens del usuario
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Contraseña restablecida exitosamente.',
        ]);
    }

    /**
     * Refrescar token (revocar actual y generar nuevo).
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        $token = $request->user()->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Token renovado exitosamente.',
            'token' => $token,
        ]);
    }

    /**
     * Actualizar perfil del usuario autenticado.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $request->user()->update($validated);

        return response()->json([
            'message' => 'Perfil actualizado exitosamente.',
            'user' => $request->user()->load('roles:id,name'),
        ]);
    }

    /**
     * Historial de logins del usuario autenticado.
     */
    public function myLoginHistory(Request $request): JsonResponse
    {
        $logs = LoginLog::where('user_id', $request->user()->id)
            ->orderByDesc('logged_in_at')
            ->paginate(20);

        return response()->json($logs);
    }

    /**
     * Historial de todos los logins (admin).
     */
    public function allLoginHistory(Request $request): JsonResponse
    {
        $query = LoginLog::with('user:id,cedula,name')
            ->orderByDesc('logged_in_at');

        // Filtros opcionales
        if ($request->filled('cedula')) {
            $query->where('cedula', $request->cedula);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from')) {
            $query->where('logged_in_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->where('logged_in_at', '<=', $request->to);
        }

        return response()->json($query->paginate(20));
    }
}
