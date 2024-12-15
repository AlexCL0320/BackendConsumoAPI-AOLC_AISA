# Login - API Comidas y sus Ingredientes

**Integrantes del Equipo:**  
López Carreño Alexis Oswaldo   
Santiago Anaya Adán Ismael   

**Materia**: Programación Web  
**Docente**: Martínez Nieto Adelina  

**Objetivo**: Generación de un Login que solicite al usuario el ingreso de un correo y una contraseña, estos datos serán verificados mediante solicitudes GET, donde si estos datos coinciden con algún registro de la lista obtenida, se permitirá el acceso a la pantalla principal, donde se podrá mostrar la lista de comidas y usuarios; En caso contrario, se mostrará un mensaje de alerta con el mensaje "Usuario Inválido"

**Instalaciones Necesarias**
PHP (Version 8.1 o superior)
Composer
Laravel 
Base de datos (para este proyecto es utilizado MySQL)

Proyecto Generado con [Laravel](https://github.com/laravel/laravel) version 11.35.0. 

# Backend

# 1.- Migraciones    
La migración consiste en en un sistema de control que permite agregar, eliminar o realizar modificaciones a las tablas, consiste en dos métodos principales up() y down() donde se definen los cambios que deberán ser realizados o revertirlos si así fuera necesario, para el desarrollo del sistema han sido utilizadas dos tablas de migraciones: Comida y Usuarios, generados a partir de las instrucciones:

```
php artisan make:migration create_usuarios_table --create=usuarios
php artisan make:migration create_comidas_table --create=comidas
```

Estos comandos generarán los archivos de migración directamente en el directorio database/migrations. Donde una vez ejecutadas las migraciones se podrán definir las columnas y tipos de datos dentro de cada archivo de migración:

## Migración de Usuario
### Código:    
```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellidoP');
            $table->string('apellidoM');
            $table->string('correo');
            $table->string('password');
            $table->string('foto')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};
```

## Migración de Comida
### Código:    
```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comida', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('ingredientes');
            $table->string('categoria');
            $table->string('precio');
            $table->string('detalles')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comida');
    }
};
```

## Base de Datos Generada:  
En este caso solo han sido utilizadas las tablas de usuario y comida, sin embargo laravel ya contiene migraciones predeterminadas para su funcionamiento, así como una tabla de registro de las migraciones realizadas

![image](https://github.com/user-attachments/assets/b410a23a-d091-4982-bf1f-e646b69a89ae)

# 2.- Modelos     
Los modelos dentro del programa representa la estructura de los datos de la aplicación, permiten la interacción con la base de datos, cada modelo se encuentra asociado a una tabla específica de la base de datos, y contiene las estructura de esta.    

Los modelos pueden ser generados a partir de la siguiente instrucción:
```
php artisan make:model Usuario
php artisan make:model Usuario
```

De igual manera puede ser generado el modelo y la migración en conjunto usando la instrucción:
```
php artisan make:model Usuario -m
php artisan make:model Comida -m
```

## Modelo de Usuario
```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuario';

    protected $fillable  =  [
        'nombre',
        'apellidoP',
        'apellidoM',
        'correo',
        'password',
        'foto'
    ];
}

```

## Modelo de Comida 
```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comida extends Model
{
    protected $table = 'comida';

    protected $fillable  =  [
        'nombre',
        'ingredientes',
        'categoria',
        'precio',
        'detalles'
    ];

}

```

## Tabla de Usuarios    
![image](https://github.com/user-attachments/assets/dbffb5aa-8755-4d19-83bd-dcaaa231b2db)

## Tabla de Comidas    
![image](https://github.com/user-attachments/assets/ab4922cd-3a16-43f0-933c-5221e369fc87)

# 3.- Controladores     
El controlador actua como intermediario entre el modelo y la vista (Generada en Angular), se ocupa de manejar la lógica y las solicitudes HTTP realizadas, estos pueden ser generados mediante los comandos:

```
php artisan make:controller UsuarioController
php artisan make:controller ComidaController
```

Los controladores son conectados mediante rutas definidas en el archivo "routes" que deberá redireccionar el controlador 

## Controllador de Usuarios    
El controlador de usuarios se encarga de la gestión de las operacioens relacionadas con los usuarios en la base ed datos, mediante una API. Implementa diversos métodos que permiten realizar las operaciones de lectura, actualización y eliminación de registros.

El método index se encarga de obtener la lista completa de usuarios registrados en la base de datos. En caso de no haber registros, si la base de datos se encuentra vacía devuelve un mensaje de error, y en caso de éxito se retorna la lista de usuarios.

El método store permite la creación de un nuevo usuario en la base de datos, los datos ingresados son validados verificando que cumplan con los requisitos específicos del campo y permite cargar una foto opcional para el usuario

El método show es utilizado para realizar la busqueda de un usuario, donde se retornará un mensaje de éxito o fallo 

El método update permite la modificación de la información de un usuario existente en la base de datos. Es realizada la validación de los valores donde si esta operacion es exitosa retornará un mensaje indicando que el usuario fue actualizado correctamente.

Para su funcionamiento son implementados los métodos HTTP GET, POST, PUT y DELETE para operacioens con API´s

```
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
        'correo' => 'nullable|email', 
        'password' => 'nullable|string|max:255',
        'foto' => 'nullable',
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
    /*
    if (isset($data['password'])) {
        $data['password'] = bcrypt($data['password']);
    }

    if ($requerimiento->hasFile('foto')) {
        $data['foto'] = $requerimiento->file('foto')->store('fotos', 'public');
    }*/

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
```

## Controlador de Comida    
El controlador de comida se encarga de las operaciones relacionadas con el modelo de comida, proporcionando métodos HTTP para realizar operaciones sobre los registros de comida, siguiendo la misma lógica que el controlador de usuarios

Método Index: consiste en la recuperación de todas las comidas registradas en la base de datos, haciendo uso del comando Comida:all(), donde si no es encontrado ningún registro de comida se mostrará un mensaje de error

Método Store: permite la creación de un nuevo registro de comida, a través del objeto request aplicando reglas de validación de datos para una correcta creación de registro

Método Show: Es utilizado para obtener un registro de comida especifico, usando el comando_ Comida::find($id) para la busqueda del registro

Método Update: Permite la actualización de un registro existente, realizando la validación de existencia del registro y aplicando reglas para la validación de los datos ingresados, donde si todos son correctos será realizada la actualización

Método destroy: Realiza la eliminación de un registro de comida específico, esto mediante su ID

```
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
```

# Rutas
Las rutas dentro del programa son utilizadas para el manejo de soliciturs HTTP hacia los métodos correspondientes de los controladores (comidaController y usuariosController), estas consisten el las siguientes:

## Rutas de Comida:        
GET /comidas: Sirve para obtener una lista de todas las comidas registradas en la base de datos.    
GET /comidas/{id}: Obtiene la información específica de una comida identificada por su ID.    
POST /comidas: Permite registrar una nueva comida en la base de datos.    
PUT /comidas/{id}: Actualiza la información de una comida existente, identificada por su ID.    
PATCH /comidas/{id}: Actualiza los datos de una comida específica.    
DELETE /comidas/{id}: Elimina una comida de la base de datos según su ID.    

## Rutas de Usuarios:        
GET /usuarios: Sirve para obtener una lista de todos los usuarios registrados en la base de datos.    
GET /usuarios/{id}: Obtiene los datos específicos de un usuario identificado por su ID.    
POST /usuarios: Permite registrar un nuevo usuario en la base de datos.    
PUT /usuarios/{id}: Actualiza los datos de un usuario existente, identificado por su ID.    
PATCH /usuarios/{id}: Actualiza los datos de un usuario específico.    
DELETE /usuarios/{id}: Elimina un usuario de la base de datos según su ID.    

```
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\usuariosController;
use App\Http\Controllers\comidaController;


// Obtener todas las comidas
Route::get('/comidas', [comidaController::class, 'index']);

// Obtener una comida específica por ID
Route::get('/comidas/{id}', [comidaController::class, 'show']);

// Crear una nueva comida
Route::post('/comidas', [comidaController::class, 'store']);

// Actualizar completamente una comida
Route::put('/comidas/{id}', [comidaController::class, 'update']);

// Actualizar parcialmente una comida
Route::patch('/comidas/{id}', [comidaController::class, 'updateParcial']);

// Eliminar una comida
Route::delete('/comidas/{id}', [comidaController::class, 'destroy']);

// Obtener todos los usuarios
Route::get('/usuarios', [usuariosController::class, 'index']);

// Obtener un usuario específico por ID
Route::get('/usuarios/{id}', [usuariosController::class, 'show']);

// Crear un nuevo usuario
Route::post('/usuarios', [usuariosController::class, 'store']);

// Actualizar completamente un usuario
Route::put('/usuarios/{id}', [usuariosController::class, 'update']);

// Actualizar parcialmente un usuario
Route::patch('/usuarios/{id}', [usuariosController::class, 'updateParcial']);

// Eliminar un usuario
Route::delete('/usuarios/{id}', [usuariosController::class, 'destroy']);
```

# Prueba mediante POSTMAN (Prueba de la API)

## Operacion GET:         
![image](https://github.com/user-attachments/assets/648f997c-f20a-4c00-8589-bb9e9239de45)

## Operacion PUT:            
![image](https://github.com/user-attachments/assets/2f83ccb3-3f35-4f67-a43b-1d0a19bd4dd2)

## Operacion DELETE:     
![image](https://github.com/user-attachments/assets/56ac0123-45d6-4d23-8123-75ad14249654)

### Confirmación de Eliminación:    
![image](https://github.com/user-attachments/assets/0a8436d5-8681-4897-bbaf-6577b940fdd4)






