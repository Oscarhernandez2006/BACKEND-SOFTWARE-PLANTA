<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrdenIngresoMp;
use App\Models\ReciboCanalMp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReciboCanalMpController extends Controller
{
    /**
     * GET /api/recibo-canal-mp — Listar todos los registros
     */
    public function index(Request $request): JsonResponse
    {
        $query = ReciboCanalMp::with('orden')->orderByDesc('recibo_ingreso_mp_fecha_registro');

        if ($request->filled('orden_id')) {
            $query->where('orden_ingreso_mp_id_registro', $request->orden_id);
        }

        if ($request->filled('fecha_desde')) {
            $query->where('recibo_ingreso_mp_fecha_registro', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('recibo_ingreso_mp_fecha_registro', '<=', $request->fecha_hasta);
        }

        if ($request->filled('cliente')) {
            $query->where('recibo_ingreso_mp_nombre_cliente', 'ilike', '%' . $request->cliente . '%');
        }

        if ($request->filled('numero_guia')) {
            $query->where('recibo_canal_mp_numero_guia', 'ilike', '%' . $request->numero_guia . '%');
        }

        if ($request->filled('numero_orden')) {
            $query->where('recibo_canal_mp_numero_orden', $request->numero_orden);
        }

        $registros = $query->paginate($request->input('per_page', 15));

        return response()->json($registros);
    }

    /**
     * GET /api/recibo-canal-mp/{id} — Obtener un registro por ID
     */
    public function show(int $id): JsonResponse
    {
        $registro = ReciboCanalMp::with('orden')->findOrFail($id);
        return response()->json($registro);
    }

    /**
     * POST /api/recibo-canal-mp — Crear un nuevo registro
     */
    public function store(Request $request): JsonResponse
    {
        // Forzar producto a CARNE EN CANAL
        $request->merge([
            'recibo_canal_mp_producto' => 'CARNE EN CANAL',
        ]);

        $validated = $request->validate([
            'orden_ingreso_mp_id_registro'           => 'required|integer|exists:orden_ingreso_mp,orden_ingreso_mp_id_registro',
            'recibo_ingreso_mp_fecha_registro'        => 'required|date',
            'recibo_ingreso_mp_nombre_cliente'        => 'required|string|max:100',
            'recibo_canal_mp_numero_guia'             => 'required|string|max:100',
            'recibo_canal_mp_lote_orden_sacrificio'   => 'required|integer',
            'recibo_canal_mp_numero_identificacion'   => 'required|integer',
            'recibo_canal_mp_codigo_animal'           => 'required|string|max:50',
            'recibo_canal_mp_cavas_canales'           => 'required|string|max:50',
            'recibo_canal_mp_producto'                => 'required|string|in:CARNE EN CANAL',
            'recibo_canal_mp_numero_orden'            => 'required|integer',
            'recibo_canal_mp_peso_canal'              => 'required|numeric',
        ], [
            'orden_ingreso_mp_id_registro.required'          => 'El campo Orden de ingreso es obligatorio',
            'orden_ingreso_mp_id_registro.integer'           => 'La Orden de ingreso debe ser un número entero',
            'orden_ingreso_mp_id_registro.exists'            => 'La Orden de ingreso seleccionada no existe',
            'recibo_ingreso_mp_fecha_registro.required'       => 'El campo Fecha de registro es obligatorio',
            'recibo_ingreso_mp_fecha_registro.date'           => 'La Fecha de registro debe ser una fecha válida',
            'recibo_ingreso_mp_nombre_cliente.required'       => 'El campo Nombre del cliente es obligatorio',
            'recibo_ingreso_mp_nombre_cliente.max'            => 'El Nombre del cliente no puede exceder 100 caracteres',
            'recibo_canal_mp_numero_guia.required'            => 'El campo Número de guía es obligatorio',
            'recibo_canal_mp_numero_guia.max'                 => 'El Número de guía no puede exceder 100 caracteres',
            'recibo_canal_mp_lote_orden_sacrificio.required'  => 'El campo Lote orden de sacrificio es obligatorio',
            'recibo_canal_mp_lote_orden_sacrificio.integer'   => 'El Lote orden de sacrificio debe ser un número entero',
            'recibo_canal_mp_numero_identificacion.required'  => 'El campo Número de identificación es obligatorio',
            'recibo_canal_mp_numero_identificacion.integer'   => 'El Número de identificación debe ser un número entero',
            'recibo_canal_mp_codigo_animal.required'          => 'El campo Código del animal es obligatorio',
            'recibo_canal_mp_codigo_animal.max'               => 'El Código del animal no puede exceder 50 caracteres',
            'recibo_canal_mp_cavas_canales.required'          => 'El campo Cavas canales es obligatorio',
            'recibo_canal_mp_cavas_canales.max'               => 'Las Cavas canales no pueden exceder 50 caracteres',
            'recibo_canal_mp_producto.in'                     => 'El Producto solo puede ser: CARNE EN CANAL',
            'recibo_canal_mp_numero_orden.required'           => 'El campo Número de orden es obligatorio',
            'recibo_canal_mp_numero_orden.integer'            => 'El Número de orden debe ser un número entero',
            'recibo_canal_mp_peso_canal.required'             => 'El campo Peso canal es obligatorio',
            'recibo_canal_mp_peso_canal.numeric'              => 'El Peso canal debe ser un valor numérico',
        ]);

        // Validar código animal único dentro de la misma orden
        $existeCodigo = ReciboCanalMp::where('orden_ingreso_mp_id_registro', $validated['orden_ingreso_mp_id_registro'])
            ->where('recibo_canal_mp_codigo_animal', $validated['recibo_canal_mp_codigo_animal'])
            ->exists();

        if ($existeCodigo) {
            // Obtener el último código consecutivo en esta orden
            $ultimoRegistro = ReciboCanalMp::where('orden_ingreso_mp_id_registro', $validated['orden_ingreso_mp_id_registro'])
                ->orderByDesc('recibo_canal_mp_id_registro')
                ->first();

            return response()->json([
                'message' => 'Esta canal ya está creada, debe cambiar el código consecutivo de la canal',
                'errors' => [
                    'recibo_canal_mp_codigo_animal' => ['El código de canal ' . $validated['recibo_canal_mp_codigo_animal'] . ' ya está registrado en esta orden'],
                ],
                'ultimo_codigo_registrado' => $ultimoRegistro->recibo_canal_mp_codigo_animal ?? null,
            ], 422);
        }

        // Validar que la orden sea de tipo RECIBO-CANAL
        $orden = OrdenIngresoMp::findOrFail($validated['orden_ingreso_mp_id_registro']);

        if ($orden->orden_ingreso_mp_tipo_proceso !== 'RECIBO-CANAL') {
            return response()->json([
                'message' => 'La Orden seleccionada no es de tipo RECIBO-CANAL',
                'errors' => [
                    'orden_ingreso_mp_id_registro' => ['La Orden seleccionada debe ser de tipo RECIBO-CANAL. Tipo actual: ' . $orden->orden_ingreso_mp_tipo_proceso],
                ],
            ], 422);
        }

        // Validar que el número de orden coincida con la orden seleccionada
        if ((int) $validated['recibo_canal_mp_numero_orden'] !== $orden->orden_ingreso_mp_numero_orden) {
            return response()->json([
                'message' => 'El Número de orden no coincide con la orden seleccionada',
                'errors' => [
                    'recibo_canal_mp_numero_orden' => ['El Número de orden debe ser ' . $orden->orden_ingreso_mp_numero_orden . ' (el mismo de la orden seleccionada)'],
                ],
            ], 422);
        }

        $registro = ReciboCanalMp::create($validated);
        $registro->load('orden');

        return response()->json([
            'message' => 'Registro creado exitosamente',
            'data' => $registro,
        ], 201);
    }
}
