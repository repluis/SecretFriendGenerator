# Sistema de Diseño Frontend

## Descripción General

El proyecto utiliza un sistema de diseño profesional y consistente basado en variables CSS, componentes reutilizables y una paleta de colores moderna.

## Archivos Principales

### CSS
- **`public/css/design-system.css`**: Sistema de diseño completo con variables CSS, componentes y utilidades

### Layouts
- **`resources/views/layouts/app.blade.php`**: Layout principal de la aplicación
- **`resources/views/layouts/partials/navbar.blade.php`**: Navbar profesional con iconos

### Componentes Blade
- **`resources/views/components/page-header.blade.php`**: Encabezado de página
- **`resources/views/components/stat-card-modern.blade.php`**: Tarjetas de estadísticas
- **`resources/views/components/button.blade.php`**: Botones con variantes
- **`resources/views/components/table-container.blade.php`**: Contenedor de tablas
- **`resources/views/components/badge.blade.php`**: Badges con colores
- **`resources/views/components/card.blade.php`**: Tarjetas genéricas
- **`resources/views/components/avatar.blade.php`**: Avatares con iniciales
- **`resources/views/components/toast.blade.php`**: Notificaciones toast

## Variables CSS

### Colores

```css
/* Primarios */
--color-primary-500: #6366f1;
--color-primary-600: #4f46e5;

/* Neutros */
--color-slate-50: #f8fafc;
--color-slate-500: #64748b;
--color-slate-900: #0f172a;

/* Semánticos */
--color-success-600: #16a34a;
--color-danger-600: #dc2626;
--color-warning-600: #d97706;
--color-info-600: #2563eb;
```

### Espaciado

```css
--spacing-xs: 0.25rem;
--spacing-sm: 0.5rem;
--spacing-md: 1rem;
--spacing-lg: 1.5rem;
--spacing-xl: 2rem;
--spacing-2xl: 3rem;
```

### Bordes

```css
--radius-sm: 6px;
--radius-md: 8px;
--radius-lg: 12px;
--radius-xl: 16px;
--radius-full: 9999px;
```

## Componentes

### Page Header

```blade
<x-page-header 
    title="Título de la Página" 
    subtitle="Descripción opcional"
/>
```

### Stat Card

```blade
<x-stat-card-modern 
    icon="📊"
    value="150"
    label="Total Usuarios"
    color="primary"
    footer="↑ 12% vs mes anterior"
/>
```

**Colores disponibles**: `primary`, `success`, `warning`, `danger`

### Button

```blade
<x-button variant="primary" size="md" icon="➕">
    Agregar
</x-button>
```

**Variantes**: `primary`, `secondary`, `success`, `danger`, `warning`, `ghost`, `ghost-primary`, `ghost-danger`, `ghost-success`

**Tamaños**: `sm`, `md`, `lg`, `icon`

### Badge

```blade
<x-badge color="success">Activo</x-badge>
<x-badge color="danger">Inactivo</x-badge>
```

**Colores**: `primary`, `success`, `danger`, `warning`, `info`, `slate`

### Avatar

```blade
<x-avatar name="Juan Pérez" size="md" />
```

**Tamaños**: `sm`, `md`, `lg`, `xl`

### Table Container

```blade
<x-table-container title="Lista de Usuarios">
    <x-slot name="actions">
        <x-button variant="primary">Agregar</x-button>
    </x-slot>
    
    <table class="table">
        <!-- contenido -->
    </table>
</x-table-container>
```

### Toast Notifications

```javascript
// Mostrar notificación
showToast('Operación exitosa', 'success');
showToast('Error al guardar', 'error');
showToast('Advertencia', 'warning');
showToast('Información', 'info');
```

## Estructura de Página Típica

```blade
@extends('layouts.app')

@section('title', 'Título de la Página')

@section('content')
    <x-page-header 
        title="Título Principal" 
        subtitle="Descripción de la página"
    />

    <!-- Stats Grid -->
    <div class="stats-grid">
        <x-stat-card-modern 
            icon="📊"
            value="150"
            label="Total"
            color="primary"
        />
        <!-- más stats -->
    </div>

    <!-- Table -->
    <x-table-container title="Lista de Datos">
        <x-slot name="actions">
            <x-button variant="primary">Agregar</x-button>
        </x-slot>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Columna 1</th>
                    <th>Columna 2</th>
                </tr>
            </thead>
            <tbody>
                <!-- filas -->
            </tbody>
        </table>
    </x-table-container>

    <x-toast />
@endsection

@section('scripts')
<script>
    // JavaScript específico
</script>
@endsection
```

## Clases Utilitarias

### Layout
- `.main-container`: Contenedor principal con max-width
- `.page-header`: Encabezado de página
- `.stats-grid`: Grid para tarjetas de estadísticas

### Cards
- `.card`: Tarjeta básica
- `.card-header`: Encabezado de tarjeta
- `.card-body`: Cuerpo de tarjeta
- `.card-footer`: Pie de tarjeta

### Tables
- `.table-container`: Contenedor de tabla
- `.table`: Tabla estilizada
- `.table-header`: Encabezado de tabla con título y acciones
- `.table-wrapper`: Wrapper con scroll horizontal

### Text
- `.text-center`: Texto centrado
- `.text-right`: Texto alineado a la derecha
- `.text-muted`: Texto con color gris
- `.text-sm`: Texto pequeño (0.875rem)
- `.text-xs`: Texto extra pequeño (0.75rem)

### Font Weight
- `.font-semibold`: Peso 600
- `.font-bold`: Peso 700

### Empty State
- `.empty-state`: Estado vacío centrado
- `.empty-state-icon`: Icono grande para estado vacío
- `.empty-state-text`: Texto para estado vacío

## Navbar

El navbar incluye:
- Logo con icono
- Links de navegación con iconos y texto
- Indicador visual de página activa
- Avatar del usuario
- Botón de logout

Los links se muestran según los permisos del usuario usando `Auth::user()->hasPermission('nombre')`.

## Responsive Design

El sistema es completamente responsive:

- **Desktop (>1024px)**: Layout completo con todos los elementos
- **Tablet (768px-1024px)**: Grid adaptado, navbar compacto
- **Mobile (<768px)**: 
  - Navbar con iconos solamente (texto oculto)
  - Tablas con scroll horizontal
  - Grid de stats en una columna
  - Botones más pequeños

## Buenas Prácticas

1. **Usar componentes Blade**: Siempre que sea posible, usar los componentes existentes
2. **Variables CSS**: Usar variables CSS para colores, espaciado y bordes
3. **Consistencia**: Mantener el mismo estilo en todas las páginas
4. **Accesibilidad**: Usar etiquetas semánticas y atributos ARIA cuando sea necesario
5. **Performance**: Minimizar CSS inline, usar clases reutilizables
6. **Responsive**: Probar en diferentes tamaños de pantalla

## Iconos

El proyecto usa emojis para iconos por simplicidad:
- 📊 Dashboard
- 🎮 Juego
- 💳 Pagos
- 💰 Recaudaciones
- 👥 Usuarios
- 👑 Admin
- ✅ Éxito
- ❌ Error
- ⚠️ Advertencia
- ℹ️ Información

## Paleta de Colores Completa

### Primary (Indigo)
- 50: #eef2ff
- 500: #6366f1
- 600: #4f46e5
- 900: #312e81

### Success (Green)
- 50: #f0fdf4
- 500: #22c55e
- 600: #16a34a
- 700: #15803d

### Danger (Red)
- 50: #fef2f2
- 500: #ef4444
- 600: #dc2626
- 700: #b91c1c

### Warning (Amber)
- 50: #fffbeb
- 500: #f59e0b
- 600: #d97706
- 700: #b45309

### Info (Blue)
- 50: #eff6ff
- 500: #3b82f6
- 600: #2563eb
- 700: #1d4ed8

### Neutral (Slate)
- 50: #f8fafc
- 100: #f1f5f9
- 200: #e2e8f0
- 500: #64748b
- 900: #0f172a
