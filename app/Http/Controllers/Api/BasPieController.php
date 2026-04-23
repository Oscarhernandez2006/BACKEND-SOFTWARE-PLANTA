<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BasPie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BasPieController extends Controller
{
    /**
     * GET /api/bas-pie — Listar todos los registros
     */
    public function index(Request $request): JsonResponse
    {
        $query = BasPie::with('camion')->orderByDesc('bas_pie_fecha_movilizacion');

        if ($request->filled('camion_id')) {
            $query->where('bas_camion_id_registro', $request->camion_id);
        }

        if ($request->filled('fecha_desde')) {
            $query->where('bas_pie_fecha_movilizacion', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('bas_pie_fecha_movilizacion', '<=', $request->fecha_hasta);
        }

        if ($request->filled('tipo_animal')) {
            $query->where('bas_pie_tipo_animal', 'ilike', '%' . $request->tipo_animal . '%');
        }

        if ($request->filled('guia')) {
            $query->where('bas_pie_guia_movilizacion', 'ilike', '%' . $request->guia . '%');
        }

        $registros = $query->paginate($request->input('per_page', 15));

        return response()->json($registros);
    }

    /**
     * GET /api/bas-pie/{id} — Obtener un registro por ID
     */
    public function show(int $id): JsonResponse
    {
        $registro = BasPie::with('camion')->findOrFail($id);
        return response()->json($registro);
    }

    /**
     * POST /api/bas-pie — Crear un nuevo registro
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bas_camion_id_registro'            => 'required|integer|exists:bas_camion,bas_camion_id_registro',
            'bas_pie_fecha_movilizacion'         => 'required|date',
            'bas_pie_guia_movilizacion'          => 'required|string|max:50',
            'bas_pie_ganado_proveedor'           => 'required|string|max:150',
            'bas_pie_ganado_cliente'             => 'required|string|max:150',
            'bas_pie_tipo_animal'                => 'required|string|max:50',
            'bas_pie_ubicacion_corral'           => 'required|string|max:50',
            'bas_pie_lote_animales'              => 'required|integer',
            'bas_pie_numero_consecutivo_animal'  => 'required|integer',
            'bas_pie_peso_animal'                => 'required|numeric',
            'bas_pie_observaciones'              => 'nullable|string|max:500',
        ], [
            'bas_camion_id_registro.required'           => 'El campo Camión es obligatorio',
            'bas_camion_id_registro.exists'             => 'El Camión seleccionado no existe',
            'bas_pie_fecha_movilizacion.required'        => 'El campo Fecha de movilización es obligatorio',
            'bas_pie_fecha_movilizacion.date'            => 'La Fecha de movilización debe ser una fecha válida',
            'bas_pie_guia_movilizacion.required'         => 'El campo Guía de movilización es obligatorio',
            'bas_pie_guia_movilizacion.max'              => 'La Guía de movilización no puede exceder 50 caracteres',
            'bas_pie_ganado_proveedor.required'          => 'El campo Proveedor de ganado es obligatorio',
            'bas_pie_ganado_proveedor.max'               => 'El Proveedor de ganado no puede exceder 150 caracteres',
            'bas_pie_ganado_cliente.required'            => 'El campo Cliente de ganado es obligatorio',
            'bas_pie_ganado_cliente.max'                 => 'El Cliente de ganado no puede exceder 150 caracteres',
            'bas_pie_tipo_animal.required'               => 'El campo Tipo de animal es obligatorio',
            'bas_pie_tipo_animal.max'                    => 'El Tipo de animal no puede exceder 50 caracteres',
            'bas_pie_ubicacion_corral.required'          => 'El campo Ubicación del corral es obligatorio',
            'bas_pie_ubicacion_corral.max'               => 'La Ubicación del corral no puede exceder 50 caracteres',
            'bas_pie_lote_animales.required'             => 'El campo Lote de animales es obligatorio',
            'bas_pie_lote_animales.integer'              => 'El Lote de animales debe ser un número entero',
            'bas_pie_numero_consecutivo_animal.required' => 'El campo Número consecutivo del animal es obligatorio',
            'bas_pie_numero_consecutivo_animal.integer'  => 'El Número consecutivo del animal debe ser un número entero',
            'bas_pie_peso_animal.required'               => 'El campo Peso del animal es obligatorio',
            'bas_pie_peso_animal.numeric'                => 'El Peso del animal debe ser un valor numérico',
            'bas_pie_observaciones.max'                  => 'Las Observaciones no pueden exceder 500 caracteres',
        ]);

        $registro = BasPie::create($validated);
        $registro->load('camion');

        return response()->json([
            'message' => 'Registro creado exitosamente',
            'data' => $registro,
        ], 201);
    }
}
