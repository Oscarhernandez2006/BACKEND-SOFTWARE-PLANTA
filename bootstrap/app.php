<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        App\Providers\FortifyServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Modelo no encontrado (findOrFail)
        $exceptions->renderable(function (ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                $modelos = [
                    'OrdenIngresoMp' => 'Orden de ingreso',
                    'BasCamion'      => 'Registro de camión',
                    'BasPie'         => 'Registro de pesaje en pie',
                    'User'           => 'Usuario',
                    'LoginLog'       => 'Registro de login',
                ];

                $clase = class_basename($e->getModel());
                $nombre = $modelos[$clase] ?? $clase;

                return response()->json([
                    'message' => "$nombre no encontrado(a)",
                    'error'   => 'resource_not_found',
                ], 404);
            }
        });

        // Ruta no encontrada (404)
        $exceptions->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Ruta no encontrada',
                    'error'   => 'route_not_found',
                ], 404);
            }
        });

        // No autenticado (401)
        $exceptions->renderable(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No autenticado. Token inválido o expirado.',
                    'error'   => 'unauthenticated',
                ], 401);
            }
        });

        // No autorizado / Sin permisos (403)
        $exceptions->renderable(function (AccessDeniedHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No tienes permisos para realizar esta acción.',
                    'error'   => 'forbidden',
                ], 403);
            }
        });
    })->create();
