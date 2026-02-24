# Estándares de Desarrollo Laravel

## Reglas Generales de Código

### REGLA CRÍTICA DE SEGURIDAD: Validación Frontend Y Backend

**NUNCA confiar solo en validaciones del frontend (botones deshabilitados, campos ocultos, etc.)**

La seguridad SIEMPRE debe implementarse en AMBAS capas:

#### Frontend (UX - Experiencia de Usuario)
- Deshabilitar botones para usuarios sin permisos
- Ocultar opciones no disponibles
- Mostrar mensajes informativos
- **Propósito**: Mejorar la experiencia del usuario, NO seguridad

#### Backend (Seguridad Real)
- Middleware de autorización en rutas
- Validación de permisos en controladores
- Verificación de roles en casos de uso
- **Propósito**: Seguridad real, prevenir accesos no autorizados

**Ejemplo de implementación correcta:**

```php
// ❌ INCORRECTO - Solo frontend (inseguro)
// Vista Blade
<button {{ Auth::user()->isAdmin() ? '' : 'disabled' }}>Eliminar</button>

// Controlador sin validación
public function delete(int $id) {
    User::destroy($id); // ¡Cualquiera puede llamar esta ruta!
}

// ✅ CORRECTO - Frontend + Backend (seguro)
// Vista Blade
<button {{ Auth::user()->isAdmin() ? '' : 'disabled' }}>Eliminar</button>

// Ruta protegida con middleware
Route::delete('/users/{id}', [UserController::class, 'delete'])
    ->middleware(['auth', 'admin']);

// Controlador con validación adicional
public function delete(int $id) {
    if (!Auth::user()->isAdmin()) {
        return response()->json([
            'success' => false,
            'message' => 'No tienes permisos.'
        ], 403);
    }
    
    User::destroy($id);
}
```

### Documentación Obligatoria

**TODA función, método y clase DEBE tener su descripción en PHPDoc:**

```php
/**
 * Descripción clara de lo que hace el método.
 *
 * @param string $param1 - Descripción del parámetro.
 * @param int $param2 - Descripción del parámetro.
 * @return array - Descripción del valor de retorno.
 */
public function miMetodo(string $param1, int $param2): array
{
    // implementación
}
```

### Arquitectura Limpia - Separación de Responsabilidades

#### 1. Repositorios (Repository Pattern)

**Los repositorios SOLO se usan para operaciones CRUD en la base de datos:**
- `find()`, `findBy()`, `create()`, `update()`, `delete()`
- Consultas y filtros de datos
- **NO contienen lógica de negocio**
- **NO contienen validaciones de negocio**
- **NO contienen cálculos o transformaciones complejas**

```php
// ✅ CORRECTO - Solo acceso a datos
public function findByIdentification(string $identification): ?User
{
    return User::where('identification', $identification)->first();
}

// ❌ INCORRECTO - Lógica de negocio en repositorio
public function findByIdentification(string $identification): ?User
{
    $user = User::where('identification', $identification)->first();
    
    // ❌ Esta lógica NO va aquí
    if ($user && $user->status === 'inactive') {
        throw new Exception('Usuario inactivo');
    }
    
    return $user;
}
```

#### 2. Casos de Uso (Use Cases)

**TODA la lógica de negocio va en los casos de uso:**
- Validaciones de reglas de negocio
- Cálculos y transformaciones
- Orquestación de múltiples operaciones
- Coordinación entre repositorios y servicios

```php
/**
 * Ejecuta el caso de uso de inicio de sesión.
 *
 * @param array $params - ['identification' => string, 'password' => string].
 * @return array{success: bool, message: string} - Resultado del inicio de sesión.
 */
public function execute(array $params = []): mixed
{
    // ✅ Validaciones de negocio
    if (empty($params['identification']) || empty($params['password'])) {
        return ['success' => false, 'message' => 'Datos incompletos'];
    }
    
    // ✅ Uso del repositorio solo para consulta
    $user = $this->authRepository->findByIdentification($params['identification']);
    
    // ✅ Lógica de negocio
    if (!$user || !$this->authService->isAccountActive($user)) {
        return ['success' => false, 'message' => 'Cuenta inválida'];
    }
    
    // ✅ Más lógica de negocio
    if (!$this->authService->verifyCredentials($user, $params['password'])) {
        return ['success' => false, 'message' => 'Credenciales incorrectas'];
    }
    
    return ['success' => true, 'message' => 'Login exitoso'];
}
```

#### 3. Controladores (Controllers)

**Los controladores DEBEN manejar TODAS las excepciones:**
- Capturar excepciones de casos de uso
- Formatear respuestas HTTP apropiadas
- Manejar errores de validación
- Retornar vistas o JSON según el contexto

```php
/**
 * Procesa el intento de inicio de sesión.
 *
 * @param LoginRequest $request - Request validado con credenciales.
 * @param LoginUser $loginUser - Caso de uso de login.
 * @return RedirectResponse - Redirección según resultado.
 */
public function login(LoginRequest $request, LoginUser $loginUser): RedirectResponse
{
    try {
        $result = $loginUser->execute([
            'identification' => $request->input('identification'),
            'password' => $request->input('password'),
            'remember' => $request->boolean('remember'),
        ]);

        if (!$result['success']) {
            return back()
                ->withInput($request->only('identification', 'remember'))
                ->withErrors(['login' => $result['message']]);
        }

        return redirect()->intended(route('home'));
        
    } catch (\Exception $e) {
        return back()
            ->withInput()
            ->withErrors(['error' => 'Error inesperado: ' . $e->getMessage()]);
    }
}
```

## Form Requests - Validaciones

### Ubicación y Organización

**TODOS los Form Requests DEBEN estar en carpeta separada:**
- Ubicación: `{Module}/Presentation/Requests/`
- Un archivo por cada tipo de request
- Nombre descriptivo: `{Acción}Request.php`

### Estructura Obligatoria de Form Requests

```php
<?php

namespace App\Modules\{Module}\Presentation\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class {Accion}Request extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer este request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // o lógica de autorización
    }

    /**
     * Reglas de validación para el request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'campo1' => 'required|string|max:100',
            'campo2' => 'required|numeric|min:0',
            // Más reglas...
        ];
    }

    /**
     * Mensajes personalizados de validación.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'campo1.required' => 'El campo1 es obligatorio.',
            'campo1.string'   => 'El campo1 debe ser texto.',
            'campo1.max'      => 'El campo1 no puede superar 100 caracteres.',
            // Más mensajes...
        ];
    }

    /**
     * Maneja una validación fallida.
     *
     * @param Validator $validator
     * @return never
     */
    public function failedValidation(Validator $validator): never
    {
        // Para requests web (formularios HTML)
        throw new HttpResponseException(
            back()
                ->withInput($this->except('password'))
                ->withErrors($validator)
        );
        
        // Para requests API (JSON)
        // throw new HttpResponseException(
        //     response()->json([
        //         'success' => false,
        //         'errors' => $validator->errors()
        //     ], 422)
        // );
    }
}
```

### Reglas de Validación Importantes

```php
// Prevención de inyección SQL y XSS
'campo' => 'required|string|max:255|regex:/^[a-zA-Z0-9\s\-\_]+$/',

// Validación de emails
'email' => 'required|email|max:255',

// Validación de números
'monto' => 'required|numeric|min:0|max:999999.99',

// Validación de fechas
'fecha' => 'required|date|date_format:Y-m-d',

// Validación de archivos
'archivo' => 'required|file|mimes:pdf,jpg,png|max:2048',

// Validación condicional
'campo' => 'required_if:otro_campo,valor',
```

## Seguridad - Prevención de Inyección SQL

### Backend (Laravel)

**SIEMPRE usar Eloquent ORM o Query Builder con bindings:**

```php
// ✅ CORRECTO - Eloquent ORM (protección automática)
$user = User::where('identification', $identification)->first();

// ✅ CORRECTO - Query Builder con bindings
$users = DB::table('users')
    ->where('identification', '=', $identification)
    ->get();

// ✅ CORRECTO - Raw queries con bindings
$users = DB::select('SELECT * FROM users WHERE identification = ?', [$identification]);

// ❌ INCORRECTO - Concatenación directa (vulnerable)
$users = DB::select("SELECT * FROM users WHERE identification = '$identification'");
```

### Frontend (Blade/JavaScript)

**TODOS los formularios DEBEN sanitizar entrada de comillas simples:**

```javascript
// ✅ CORRECTO - Prevenir comillas simples antes de enviar
document.querySelector('form').addEventListener('submit', function(e) {
    const inputs = this.querySelectorAll('input[type="text"], textarea');
    
    inputs.forEach(input => {
        // Remover o escapar comillas simples
        input.value = input.value.replace(/'/g, '');
        // O usar: input.value = input.value.replace(/'/g, "\\'");
    });
});

// ✅ CORRECTO - Validación en tiempo real
function sanitizeInput(value) {
    return value.replace(/['"]/g, '');
}

document.getElementById('miInput').addEventListener('input', function(e) {
    this.value = sanitizeInput(this.value);
});
```

```blade
{{-- ✅ CORRECTO - Blade escapa automáticamente --}}
<input type="text" value="{{ old('campo', $valor) }}">

{{-- ✅ CORRECTO - Validación adicional con Alpine.js --}}
<input 
    type="text" 
    x-model="campo"
    @input="campo = campo.replace(/'/g, '')"
>

{{-- ❌ INCORRECTO - Sin escapar (vulnerable) --}}
<input type="text" value="{!! $valor !!}">
```

### Validación en Form Requests

```php
/**
 * Prepara los datos para validación, removiendo caracteres peligrosos.
 *
 * @return void
 */
protected function prepareForValidation(): void
{
    $this->merge([
        'campo' => str_replace("'", '', $this->campo),
        // O usar: 'campo' => htmlspecialchars($this->campo, ENT_QUOTES, 'UTF-8'),
    ]);
}
```

## Convenciones de Código

### Nombres de Variables y Métodos

```php
// ✅ camelCase para variables y métodos
$userName = 'Juan';
public function getUserName(): string {}

// ✅ PascalCase para clases
class UserController {}

// ✅ UPPER_CASE para constantes
const MAX_LOGIN_ATTEMPTS = 5;
```

### Tipos de Retorno

**SIEMPRE especificar tipos de retorno:**

```php
// ✅ CORRECTO
public function getUser(int $id): ?User
{
    return User::find($id);
}

// ✅ CORRECTO - Mixed cuando puede retornar varios tipos
public function execute(array $params = []): mixed
{
    return ['success' => true];
}

// ❌ INCORRECTO - Sin tipo de retorno
public function getUser($id)
{
    return User::find($id);
}
```

### Inyección de Dependencias

```php
// ✅ CORRECTO - Inyección por constructor
public function __construct(
    private AuthRepositoryInterface $authRepository,
    private AuthService $authService,
) {}

// ✅ CORRECTO - Inyección en método (para controladores)
public function login(LoginRequest $request, LoginUser $loginUser): RedirectResponse
{
    // ...
}
```

## Respuestas Consistentes

### Casos de Uso

```php
// ✅ Formato estándar de respuesta
return [
    'success' => true|false,
    'message' => 'Mensaje descriptivo',
    'data' => [], // opcional
];
```

### Controladores API

```php
// ✅ Respuesta JSON consistente
return response()->json([
    'success' => true,
    'message' => 'Operación exitosa',
    'data' => $resultado,
], 200);

// ✅ Respuesta de error
return response()->json([
    'success' => false,
    'message' => 'Error en la operación',
    'errors' => $validator->errors(),
], 422);
```

## Testing

### Estructura de Tests

```php
/**
 * Test que verifica el login exitoso de un usuario.
 *
 * @return void
 */
public function test_usuario_puede_iniciar_sesion_con_credenciales_validas(): void
{
    // Arrange
    $user = User::factory()->create([
        'identification' => '12345',
        'password' => bcrypt('password123'),
    ]);

    // Act
    $response = $this->post('/login', [
        'identification' => '12345',
        'password' => 'password123',
    ]);

    // Assert
    $response->assertRedirect('/home');
    $this->assertAuthenticatedAs($user);
}
```

## Checklist de Código

Antes de hacer commit, verificar:

- [ ] Todas las funciones tienen PHPDoc con descripción
- [ ] Los repositorios solo tienen operaciones CRUD
- [ ] La lógica de negocio está en casos de uso
- [ ] Los controladores manejan excepciones
- [ ] Los Form Requests están en carpeta separada
- [ ] Las validaciones previenen inyección SQL
- [ ] El frontend sanitiza comillas simples
- [ ] Se especifican tipos de retorno
- [ ] Se usa inyección de dependencias
- [ ] Las respuestas son consistentes
- [ ] El código sigue las convenciones de nombres
