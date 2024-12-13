<?php

namespace App\Http\Controllers;

use App\Models\Comida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class comidaController extends Controller
{
    // Obtener todas las comidas
    public function index()
    {
        $comidas = Comida::all();
        if ($comidas->isEmpty()) {
            return response()->json(['mensaje' => 'No hay comidas registradas'], 404);
        }
        return response()->json($comidas, 200);
    }

    // Crear una nueva comida
    public function store(Request $request)
    {
        // Validación
        $validar = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'ingredientes' => 'required|string|max:255',
            'categoria' => 'required|string|max:255',
            'precio' => 'required|string|max:255',
            'detalles' => 'nullable|string|max:255',
        ]);

        if ($validar->fails()) {
            return response()->json([
                'mensaje' => 'Errores de validación',
                'errores' => $validar->errors(),
                'status' => 400
            ], 400);
        }

        // Crear la comida
        $comida = Comida::create([
            'nombre' => $request->input('nombre'),
            'ingredientes' => $request->input('ingredientes'),
            'categoria' => $request->input('categoria'),
            'precio' => $request->input('precio'),
            'detalles' => $request->input('detalles'),
        ]);

        return response()->json([
            'mensaje' => 'Comida creada exitosamente',
            'comida' => $comida,
            'status' => 201
        ], 201);
    }

    // Mostrar una comida por id
    public function show($id)
    {
        $comida = Comida::find($id);
        if (!$comida) {
            return response()->json(['mensaje' => 'Comida no encontrada'], 404);
        }
        return response()->json($comida, 200);
    }

    // Actualizar una comida existente
    public function update(Request $request, $id)
    {
        $comida = Comida::find($id);
        if (!$comida) {
            return response()->json(['mensaje' => 'Comida no encontrada'], 404);
        }

        // Validación
        $validar = Validator::make($request->all(), [
            'nombre' => 'nullable|string|max:255',
            'ingredientes' => 'nullable|string|max:255',
            'categoria' => 'nullable|string|max:255',
            'precio' => 'nullable|string|max:255',
            'detalles' => 'nullable|string|max:255',
        ]);

        if ($validar->fails()) {
            return response()->json([
                'mensaje' => 'Errores de validación',
                'errores' => $validar->errors(),
                'status' => 400
            ], 400);
        }

        // Filtrando solo los valores que no sean nulos
        $data = array_filter($request->all(), fn($value) => $value !== null);

        // Actualizar la comida
        $comida->update($data);

        return response()->json([
            'mensaje' => 'Comida actualizada',
            'comida' => $comida,
            'status' => 200
        ], 200);
    }

    // Eliminar una comida
    public function destroy($id)
    {
        $comida = Comida::find($id);
        if (!$comida) {
            return response()->json(['mensaje' => 'Comida no encontrada'], 404);
        }

        $comida->delete();

        return response()->json(['mensaje' => 'Comida eliminada'], 200);
    }
}
