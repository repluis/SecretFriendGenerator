# Control de Acceso Basado en Roles (RBAC)

## Descripción General

El sistema implementa control de acceso basado en roles para restringir ciertas operaciones solo a usuarios administradores.

## Sistema de Permisos

El sistema implementa un control granular de acceso mediante permisos asignados a roles. Cada rol puede tener permisos específicos que determinan qué pestañas/secciones del sistema puede ver el usuario.

### Permisos Disponibles

- **dashboard**: Acceso al panel principal
- **juego**: Acceso al módulo de Secret Santa
- **pagos**: Acceso al módulo de pagos
- **recaudaciones**: Acceso al módulo de recaudaciones
- **usuarios**: Acceso a la lista de usuarios
- **admin**: Acceso al panel de administración
- **\***: Acceso total a todas las secciones (solo admin)

### Roles del Sistema

#### Administrador (admin)
- **Permisos**: `['*']` (acceso total)
- Acceso completo al sistema
- Puede registrar pagos y transacciones
- Puede modificar información de todos los usuarios
- Puede editar identificaciones de usuarios
- Puede restablecer contraseñas de usuarios
- Puede ejecutar cobros manuales
- Puede eliminar datos del sistema
- Puede activar/desactivar transacciones
- Puede asignar roles a usuarios
- Puede gestionar permisos de otros roles
- Acceso al panel de administración
- **Nota**: Los permisos del admin no son editables (siempre tiene acceso total)

#### Finanzas (finance)
- **Permisos por defecto**: `['dashboard', 'pagos', 'recaudaciones']`
- Puede registrar pagos y transacciones
- Puede ver reportes financieros
- Puede ejecutar cobros manuales
- Puede modificar moras y penalidades
- Puede activar/desactivar transacciones
- No puede modificar usuarios ni asignar roles
- No puede eliminar datos del sistema
- **Nota**: Los permisos son editables desde el panel de admin

#### Usuario (user)
- **Permisos por defecto**: `['dashboard', 'juego']`
- Solo puede editar su propia información en el perfil
- Puede ver reportes y resúmenes (solo lectura)
- No puede modificar información de otros usuarios
- No puede realizar operaciones administrativas
- No puede registrar pagos ni transacciones
- **Nota**: Los permisos son editables desde el panel de admin

### Roles Personalizados

Los administradores pueden crear roles personalizados con permisos específicos desde el panel de administración. Estos roles:
- Pueden tener cualquier combinación de permisos
- Son completamente editables
- Pueden ser eliminados (a diferencia de los roles del sistema)
- Se identifican con el badge "Personalizado" en el panel de admin

## Implementación Técnica

### Modelo Role

Se agregó el campo `permissions` (JSON) y el método `hasPermission()`:

```php
protected $fillable = ['name', 'permissions'];

protected $casts = [
    'permissions' => 'array',
];

/**
 * Verifica si el rol tiene un permiso específico.
 *
 * @param string $permission - Nombre del permiso.
 * @return bool
 */
public function hasPermission(string $permission): bool
{
    $permissions = $this->permissions ?? [];
    
    // Si tiene asterisco, tiene todos los permisos
    if (in_array('*', $permissions)) {
        return true;
    }
    
    return in_array($permission, $permissions);
}
```

### Modelo User

Se agregó el método `hasPermission()` para verificar permisos a través de los roles del usuario:

```php
/**
 * Verifica si el usuario tiene permiso para ver una sección.
 *
 * @param string $permission - Nombre del permiso (ej: 'dashboard', 'pagos').
 * @return bool - True si tiene permiso, false en caso contrario.
 */
public function hasPermission(string $permission): bool
{
    foreach ($this->roles as $role) {
        if ($role->hasPermission($permission)) {
            return true;
        }
    }
    
    return false;
}
```

**Nota**: Si un usuario tiene múltiples roles, basta con que UNO de sus roles tenga el permiso para que el usuario tenga acceso.

### Control en Vistas (Frontend)

#### Navbar - Pestañas Basadas en Permisos

El navbar muestra solo las pestañas a las que el usuario tiene permiso de acceder:

```blade
@auth
    @if(Auth::user()->hasPermission('dashboard'))
        <a href="/">Dashboard</a>
    @endif
    @if(Auth::user()->hasPermission('juego'))
        <a href="{{ route('juego') }}">Juego</a>
    @endif
    @if(Auth::user()->hasPermission('pagos'))
        <a href="{{ route('fundraising.pagos') }}">Pagos</a>
    @endif
    @if(Auth::user()->hasPermission('recaudaciones'))
        <a href="{{ route('fundraising.recaudaciones') }}">Recaudaciones</a>
    @endif
    @if(Auth::user()->hasPermission('usuarios'))
        <a href="{{ route('usuarios') }}">Usuarios</a>
    @endif
    @if(Auth::user()->hasPermission('admin'))
        <a href="{{ route('admin') }}">👑 Admin</a>
    @endif
@endauth
```

#### Botones y Campos Deshabilitados

Los botones y campos de entrada se deshabilitan para usuarios sin permisos usando la directiva Blade:

```blade
{{ Auth::user()->isAdmin() ? '' : 'disabled' }}
```

#### Vistas Protegidas

1. **Pagos (`pagos.blade.php`)**
   - Input de monto: deshabilitado para no admin
   - Botón "Pagar": deshabilitado para no admin

2. **Recaudaciones (`recaudaciones.blade.php`)**
   - Input de monto: deshabilitado para no admin
   - Botón "Pagar": deshabilitado para no admin
   - Botón "Ejecutar cobro manual": deshabilitado para no admin
   - Botón "Eliminar todos los datos": deshabilitado para no admin
   - Botón "Activar/Desactivar transacción": deshabilitado para no admin

3. **Usuarios (`users/index.blade.php`)**
   - Botón de editar identificación: oculto para no admin
   - Formulario de edición inline: oculto para no admin
   - Botón "Restablecer contraseña": reemplazado por texto informativo para no admin

4. **Perfil (`users/profile.blade.php`)**
   - Todos los usuarios pueden editar su propia información
   - No requiere restricciones adicionales

### Estilos para Botones Deshabilitados

Se agregaron estilos CSS para indicar visualmente que los botones están deshabilitados:

```css
.btn-pay:disabled {
    background: #cbd5e1;
    color: #94a3b8;
    cursor: not-allowed;
    opacity: 0.6;
}

.btn-toggle:disabled {
    background: #f1f5f9;
    color: #cbd5e1;
    cursor: not-allowed;
    opacity: 0.6;
}

.pay-form input:disabled {
    background: #f1f5f9;
    color: #94a3b8;
    cursor: not-allowed;
}
```

## Seguridad Backend (IMPLEMENTADO)

### 1. Middleware de Autorización ✅

Se creó el middleware `EnsureUserIsAdmin` que verifica el rol de administrador:

```php
// app/Http/Middleware/EnsureUserIsAdmin.php
public function handle(Request $request, Closure $next): Response
{
    if (!Auth::check()) {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado.',
            ], 401);
        }
        return redirect()->route('login');
    }

    if (!Auth::user()->isAdmin()) {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción. Solo administradores.',
            ], 403);
        }
        abort(403, 'No tienes permisos para realizar esta acción.');
    }

    return $next($request);
}
```

### 2. Protección de Rutas API ✅

Las rutas administrativas están protegidas con el middleware 'admin':

```php
// routes/api.php

// Transacciones - Solo admin puede crear/modificar
Route::prefix('transactions')->group(function () {
    Route::get('/', [TransactionApiController::class, 'index']);
    
    Route::middleware(['admin'])->group(function () {
        Route::post('/', [TransactionApiController::class, 'store']);
        Route::patch('/{id}/toggle-active', [TransactionApiController::class, 'toggleActive']);
    });
});

// Usuarios - Solo admin puede modificar
Route::prefix('users')->middleware(['admin'])->group(function () {
    Route::post('/', [UserApiController::class, 'store']);
    Route::put('/{id}', [UserApiController::class, 'update']);
    Route::patch('/{id}/identification', [UserApiController::class, 'updateIdentification']);
    Route::patch('/{id}/reset-password', [UserApiController::class, 'resetPassword']);
});

// Fundraising - Solo admin
Route::prefix('fundraising')->middleware(['admin'])->group(function () {
    Route::post('/run-manual', [FundraisingApiController::class, 'runManual']);
    Route::delete('/reset-data', [FundraisingApiController::class, 'resetData']);
    Route::patch('/charges/{chargeId}/penalty', [FundraisingApiController::class, 'updatePenalty']);
});
```

### 3. Registro del Middleware ✅

El middleware se registró en `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
    ]);
})
```

## Seguridad Backend (Opcional - Ya protegido por middleware)

Verificar permisos en cada método del controlador:

```php
public function store(Request $request)
{
    if (!Auth::user()->isAdmin()) {
        return response()->json([
            'success' => false,
            'message' => 'No tienes permisos para realizar esta acción.'
        ], 403);
    }
    
    // Lógica del método...
}
```

### 4. Validación en Casos de Uso

Agregar verificación de permisos en los casos de uso:

```php
public function execute(array $params = []): mixed
{
    // Verificar permisos
    if (!$params['user']->isAdmin()) {
        return [
            'success' => false,
            'message' => 'No tienes permisos para realizar esta acción.'
        ];
    }
    
    // Lógica del caso de uso...
}
```

## Buenas Prácticas

1. **Nunca confiar solo en el frontend**: Los botones deshabilitados son solo UX, no seguridad
2. **Validar siempre en el backend**: Middleware, controladores y casos de uso deben verificar permisos
3. **Mensajes claros**: Informar al usuario por qué no puede realizar una acción
4. **Logging**: Registrar intentos de acceso no autorizado
5. **Consistencia**: Aplicar las mismas reglas en toda la aplicación

## Testing

### Tests Recomendados

```php
// Test que verifica que no admin no puede crear transacciones
public function test_non_admin_cannot_create_transaction(): void
{
    $user = User::factory()->create(); // Sin rol admin
    
    $response = $this->actingAs($user)
        ->postJson('/api/transactions', [
            'user_id' => 1,
            'type' => 'credit',
            'amount' => 10.00
        ]);
    
    $response->assertStatus(403);
}

// Test que verifica que admin puede crear transacciones
public function test_admin_can_create_transaction(): void
{
    $admin = User::factory()->create();
    $admin->roles()->attach(Role::where('name', 'admin')->first());
    
    $response = $this->actingAs($admin)
        ->postJson('/api/transactions', [
            'user_id' => 1,
            'type' => 'credit',
            'amount' => 10.00
        ]);
    
    $response->assertStatus(200);
}
```

## Checklist de Implementación

- [x] Método `isAdmin()` en modelo User
- [x] Método `hasPermission()` en modelo User
- [x] Método `hasPermission()` en modelo Role
- [x] Campo `permissions` (JSON) en tabla roles
- [x] Migración para agregar permisos a roles
- [x] Permisos por defecto para roles del sistema
- [x] Panel de administración con gestión de permisos
- [x] Checkboxes para editar permisos de roles
- [x] API endpoint para actualizar permisos
- [x] Navbar con pestañas basadas en permisos
- [x] Botones deshabilitados en vistas
- [x] Estilos CSS para estados deshabilitados
- [x] Tooltips informativos en botones
- [x] Middleware de autorización (EnsureUserIsAdmin)
- [x] Protección de rutas API con middleware 'admin'
- [ ] Validación adicional en controladores (opcional, middleware ya protege)
- [ ] Validación en casos de uso (opcional, middleware ya protege)
- [ ] Tests unitarios y de integración
- [ ] Logging de intentos no autorizados
