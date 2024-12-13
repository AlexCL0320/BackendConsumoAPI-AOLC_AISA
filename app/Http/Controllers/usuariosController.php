<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class usuariosController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::all();
        if ($usuarios->isEmpty()) {
            return response()->json(['mensaje' => 'No hay usuarios registrados'], 404);
        }
        return response()->json($usuarios, 200);
    }

    public function store(Request $requerimiento)
    {
        // Validación de los datos, incluyendo el campo 'foto' opcional
        $validar = Validator::make($requerimiento->all(), [
            'nombre' => 'required|string|max:255',
            'apellidoP' => 'required|string|max:255',
            'apellidoM' => 'required|string|max:255',
            'correo' => 'required|string|max:255',
            'password' => 'required|min:8',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',  // Validación para la foto
        ]);

        if ($validar->fails()) {
            $data = [
                'mensaje' => 'Errores de validación de datos',
                'error' => $validar->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        // Si hay una foto, se maneja
        $foto_path = null;
        if ($requerimiento->hasFile('foto')) {
            $foto_path = $requerimiento->file('foto')->store('fotos', 'public'); // Guardamos la foto en la carpeta 'public/fotos'
        }

        // Crear el nuevo usuario
        $usuario = Usuario::create([
            'nombre' => $requerimiento->input('nombre'),
            'apellidoP' => $requerimiento->input('apellidoP'),
            'apellidoM' => $requerimiento->input('apellidoM'),
            'correo' => $requerimiento->input('correo'),
            'password' => bcrypt($requerimiento->input('password')),
            'foto' => $foto_path, // Asignar la ruta de la foto si fue subida
        ]);

        $data = [
            'usuario' => $usuario,
            'status' => 201
        ];
        return response()->json($data, 201);
    }

    public function show($id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
        }
        return response()->json($usuario, 200);
    }

    public function update(Request $requerimiento, $id)
{
    $usuario = Usuario::find($id);
    if (!$usuario) {
        return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
    }

    // Validación de los datos
    $validar = Validator::make($requerimiento->all(), [
        'nombre' => 'nullable|string|max:255',
        'apellidoP' => 'nullable|string|max:255',
        'apellidoM' => 'nullable|string|max:255',
        'correo' => 'nullable|email', // No es único.
        'password' => 'nullable|min:8',
        'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($validar->fails()) {
        return response()->json([
            'mensaje' => 'Errores de validación de datos',
            'error' => $validar->errors(),
            'status' => 400,
        ], 400);
    }

    // Filtrar los campos nulos
    $data = array_filter($requerimiento->all(), fn($value) => $value !== null);

    if (isset($data['password'])) {
        $data['password'] = bcrypt($data['password']);
    }

    if ($requerimiento->hasFile('foto')) {
        $data['foto'] = $requerimiento->file('foto')->store('fotos', 'public');
    }

    $usuario->update($data);

    return response()->json(['mensaje' => 'Usuario actualizado', 'usuario' => $usuario], 200);
}

    public function updateParcial(Request $requerimiento, $id)
    {
        return $this->update($requerimiento, $id);
    }

    public function destroy($id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
        }

        $usuario->delete();

        return response()->json(['mensaje' => 'Usuario eliminado'], 200);
    }
}
