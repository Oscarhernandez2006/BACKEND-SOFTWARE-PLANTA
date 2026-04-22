<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BasCamion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BasCamionController extends Controller
{
    /**
     * GET /api/bas-camion — Listar todos los registros
     */
    public function index(Request $request): JsonResponse
    {
        $query = BasCamion::query()->orderByDesc('bas_camion_fecha_movilizacion');

        // Filtro opcional por fecha
        if ($request->filled('fecha_desde')) {
            $query->where('bas_camion_fecha_movilizacion', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->where('bas_camion_fecha_movilizacion', '<=', $request->fecha_hasta);
        }

        // Filtro opcional por placa
        if ($request->filled('placa')) {
            $query->where('bas_camion_placa_vehiculo', 'ilike', '%' . $request->placa . '%');
        }

        $registros = $query->paginate($request->input('per_page', 15));

        return response()->json($registros);
    }

    /**
     * GET /api/bas-camion/{id} — Obtener un registro por ID
     */
    public function show(int $id): JsonResponse
    {
        $registro = BasCamion::findOrFail($id);
        return response()->json($registro);
    }

    /**
     * POST /api/bas-camion — Crear un nuevo registro
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bas_camion_fecha_movilizacion' => 'required|date',
            'bas_camion_guia_movilizacion'  => 'required|string|max:50',
            'bas_camion_lugar_procedencia'  => 'required|string|max:150',
            'bas_camion_ganado_proveedor'   => 'required|string|max:150',
            'bas_camion_ganado_cliente'     => 'required|string|max:150',
            'bas_camion_placa_vehiculo'     => 'required|string|max:10',
            'bas_camion_conductor_nombre'   => 'required|string|max:100',
            'bas_camion_referencia'         => 'required|integer',
            'bas_camion_cantidad_lote'      => 'required|integer',
            'bas_camion_peso_entrada'       => 'required|numeric',
            'bas_camion_peso_salida'        => 'required|numeric',
            'bas_camion_peso_neto'          => 'required|numeric',
            'bas_camion_peso_promedio'      => 'required|numeric',
        ], [
            'bas_camion_fecha_movilizacion.required' => 'El campo Fecha de movilización es obligatorio',
            'bas_camion_fecha_movilizacion.date'     => 'La Fecha de movilización debe ser una fecha válida',
            'bas_camion_guia_movilizacion.required'  => 'El campo Guía de movilización es obligatorio',
            'bas_camion_guia_movilizacion.max'       => 'La Guía de movilización no puede exceder 50 caracteres',
            'bas_camion_lugar_procedencia.required'  => 'El campo Lugar de procedencia es obligatorio',
            'bas_camion_lugar_procedencia.max'       => 'El Lugar de procedencia no puede exceder 150 caracteres',
            'bas_camion_ganado_proveedor.required'   => 'El campo Proveedor de ganado es obligatorio',
            'bas_camion_ganado_proveedor.max'        => 'El Proveedor de ganado no puede exceder 150 caracteres',
            'bas_camion_ganado_cliente.required'     => 'El campo Cliente de ganado es obligatorio',
            'bas_camion_ganado_cliente.max'          => 'El Cliente de ganado no puede exceder 150 caracteres',
            'bas_camion_placa_vehiculo.required'     => 'El campo Placa del vehículo es obligatorio',
            'bas_camion_placa_vehiculo.max'          => 'La Placa del vehículo no puede exceder 10 caracteres',
            'bas_camion_conductor_nombre.required'   => 'El campo Nombre del conductor es obligatorio',
            'bas_camion_conductor_nombre.max'        => 'El Nombre del conductor no puede exceder 100 caracteres',
            'bas_camion_referencia.required'         => 'El campo Referencia es obligatorio',
            'bas_camion_referencia.integer'          => 'La Referencia debe ser un número entero',
            'bas_camion_cantidad_lote.required'      => 'El campo Cantidad del lote es obligatorio',
            'bas_camion_cantidad_lote.integer'       => 'La Cantidad del lote debe ser un número entero',
            'bas_camion_peso_entrada.required'       => 'El campo Peso de entrada es obligatorio',
            'bas_camion_peso_entrada.numeric'        => 'El Peso de entrada debe ser un valor numérico',
            'bas_camion_peso_salida.required'        => 'El campo Peso de salida es obligatorio',
            'bas_camion_peso_salida.numeric'         => 'El Peso de salida debe ser un valor numérico',
            'bas_camion_peso_neto.required'          => 'El campo Peso neto es obligatorio',
            'bas_camion_peso_neto.numeric'           => 'El Peso neto debe ser un valor numérico',
            'bas_camion_peso_promedio.required'      => 'El campo Peso promedio es obligatorio',
            'bas_camion_peso_promedio.numeric'       => 'El Peso promedio debe ser un valor numérico',
        ]);

        $registro = BasCamion::create($validated);

        return response()->json([
            'message' => 'Registro creado exitosamente',
            'data' => $registro,
        ], 201);
    }
}
