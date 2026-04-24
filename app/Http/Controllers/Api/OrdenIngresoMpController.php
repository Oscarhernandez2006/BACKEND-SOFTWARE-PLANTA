<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrdenIngresoMp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrdenIngresoMpController extends Controller
{
    /**
     * GET /api/orden-ingreso-mp — Listar todas las órdenes
     */
    public function index(Request $request): JsonResponse
    {
        $query = OrdenIngresoMp::query()->orderByDesc('orden_ingreso_mp_fecha_proceso');

        if ($request->filled('estado')) {
            $query->where('orden_ingreso_mp_estado_orden', 'ilike', '%' . $request->estado . '%');
        }

        if ($request->filled('cliente')) {
            $query->where('orden_ingreso_mp_nombre_cliente', 'ilike', '%' . $request->cliente . '%');
        }

        if ($request->filled('tipo_proceso')) {
            $query->where('orden_ingreso_mp_tipo_proceso', 'ilike', '%' . $request->tipo_proceso . '%');
        }

        if ($request->filled('fecha_desde')) {
            $query->where('orden_ingreso_mp_fecha_proceso', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('orden_ingreso_mp_fecha_proceso', '<=', $request->fecha_hasta);
        }

        if ($request->filled('numero_orden')) {
            $query->where('orden_ingreso_mp_numero_orden', $request->numero_orden);
        }

        $registros = $query->paginate($request->input('per_page', 15));

        return response()->json($registros);
    }

    /**
     * GET /api/orden-ingreso-mp/{id} — Obtener una orden por ID
     */
    public function show(int $id): JsonResponse
    {
        $registro = OrdenIngresoMp::findOrFail($id);
        return response()->json($registro);
    }

    /**
     * POST /api/orden-ingreso-mp — Crear una nueva orden
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'orden_ingreso_mp_numero_orden'      => 'required|integer|unique:orden_ingreso_mp,orden_ingreso_mp_numero_orden',
            'orden_ingreso_mp_tipo_proceso'      => 'required|string|max:50',
            'orden_ingreso_mp_fecha_sacrificio'   => 'required|date',
            'orden_ingreso_mp_fecha_proceso'      => 'required|date',
            'orden_ingreso_mp_nombre_cliente'     => 'required|string|max:100',
            'orden_ingreso_mp_estado_orden'       => 'required|string|max:50',
        ], [
            'orden_ingreso_mp_numero_orden.required'  => 'El campo Número de orden es obligatorio',
            'orden_ingreso_mp_numero_orden.integer'   => 'El Número de orden debe ser un número entero',
            'orden_ingreso_mp_numero_orden.unique'    => 'El Número de orden ya existe',
            'orden_ingreso_mp_tipo_proceso.required'  => 'El campo Tipo de proceso es obligatorio',
            'orden_ingreso_mp_tipo_proceso.max'       => 'El Tipo de proceso no puede exceder 50 caracteres',
            'orden_ingreso_mp_fecha_sacrificio.required' => 'El campo Fecha de sacrificio es obligatorio',
            'orden_ingreso_mp_fecha_sacrificio.date'     => 'La Fecha de sacrificio debe ser una fecha válida',
            'orden_ingreso_mp_fecha_proceso.required' => 'El campo Fecha de proceso es obligatorio',
            'orden_ingreso_mp_fecha_proceso.date'     => 'La Fecha de proceso debe ser una fecha válida',
            'orden_ingreso_mp_nombre_cliente.required' => 'El campo Nombre del cliente es obligatorio',
            'orden_ingreso_mp_nombre_cliente.max'      => 'El Nombre del cliente no puede exceder 100 caracteres',
            'orden_ingreso_mp_estado_orden.required'  => 'El campo Estado de la orden es obligatorio',
            'orden_ingreso_mp_estado_orden.max'       => 'El Estado de la orden no puede exceder 50 caracteres',
        ]);

        $registro = OrdenIngresoMp::create($validated);

        return response()->json([
            'message' => 'Orden creada exitosamente',
            'data' => $registro,
        ], 201);
    }

    /**
     * PATCH /api/orden-ingreso-mp/{id}/estado — Cambiar estado de la orden
     */
    public function updateEstado(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'orden_ingreso_mp_estado_orden' => 'required|string|max:50',
        ], [
            'orden_ingreso_mp_estado_orden.required' => 'El campo Estado de la orden es obligatorio',
            'orden_ingreso_mp_estado_orden.max'      => 'El Estado de la orden no puede exceder 50 caracteres',
        ]);

        $registro = OrdenIngresoMp::findOrFail($id);
        $registro->update($validated);

        return response()->json([
            'message' => 'Estado actualizado exitosamente',
            'data' => $registro,
        ]);
    }
}
