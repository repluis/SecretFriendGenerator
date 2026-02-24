# Contexto del Proyecto - Secret Friend Generator

## Descripción General

Aplicación Laravel para gestión de Secret Santa (Amigo Secreto) con módulos de autenticación, recaudación de fondos (fundraising), transacciones y dashboard.

## Arquitectura del Proyecto

### Arquitectura Limpia (Clean Architecture)

El proyecto sigue los principios de Clean Architecture con Domain-Driven Design (DDD), organizado en módulos independientes dentro de `app/Modules/`.

### Estructura de Capas por Módulo

Cada módulo funcional se organiza en 4 capas principales:

```
app/Modules/{ModuleName}/
├── Application/          # Casos de uso (lógica de aplicación)
│   └── UseCases/        # Implementación de casos de uso específicos
├── Domain/              # Lógica de negocio pura
│   ├── Entities/        # Entidades del dominio
│   ├── Repositories/    # Interfaces de repositorios
│   └── Services/        # Servicios del dominio
├── Infrastructure/      # Implementaciones técnicas
│   ├── Persistence/     # Repositorios Eloquent y modelos
│   │   └── Models/      # Modelos Eloquent
│   ├── Jobs/            # Jobs de Laravel
│   └── Middleware/      # Middleware específico del módulo
└── Presentation/        # Capa de presentación
    ├── Controllers/     # Controladores HTTP
    └── Requests/        # Form Requests con validaciones
```

## Módulos Principales

### 1. Auth (Autenticación)
- Login/Logout de usuarios
- Middleware de autenticación
- Gestión de roles

### 2. Fundraising (Recaudación)
- Gestión de cargos mensuales
- Aplicación de moras/penalidades
- Sincronización con transacciones
- Reportes financieros

### 3. SecretSanta (Amigo Secreto)
- Gestión de jugadores
- Asignación aleatoria de amigos
- Generación de URLs únicas
- Configuración del juego

### 4. Transaction (Transacciones)
- Registro de pagos
- Balance de usuarios
- Historial de transacciones

### 5. Dashboard
- Panel principal de la aplicación

### 6. Home
- Página de inicio

## Principios de Diseño

### Separación de Responsabilidades

1. **Domain Layer**: Lógica de negocio pura, sin dependencias de frameworks
2. **Application Layer**: Orquestación de casos de uso
3. **Infrastructure Layer**: Implementaciones técnicas (base de datos, APIs externas)
4. **Presentation Layer**: Interfaz con el usuario (HTTP, CLI, etc.)

### Inyección de Dependencias

- Todos los casos de uso reciben sus dependencias por constructor
- Se utilizan interfaces para los repositorios
- Laravel Service Container maneja la resolución de dependencias

### Patrón Repository

- Abstracción del acceso a datos
- Interfaces en Domain, implementaciones en Infrastructure
- Conversión entre modelos Eloquent y entidades del dominio

## Tecnologías Principales

- **Framework**: Laravel (PHP)
- **Base de Datos**: MySQL/MariaDB (Eloquent ORM)
- **Frontend**: Blade Templates
- **Autenticación**: Laravel Auth
- **Jobs**: Laravel Queue System

## Convenciones de Nombres

- **Casos de Uso**: Verbos en infinitivo (CreatePlayer, GetAllPlayers, UpdateGameConfig)
- **Repositorios**: Sustantivo + Repository + Interface (AuthRepositoryInterface)
- **Controladores**: Sustantivo + Controller (AuthController, FinanceController)
- **Requests**: Acción + Request (LoginRequest, UpdatePenaltyRequest)
- **Modelos**: Sustantivo singular (User, Role, FundraisingChargeModel)

## Flujo de Datos Típico

1. **Request** → Validación en FormRequest
2. **Controller** → Recibe request validado
3. **Use Case** → Ejecuta lógica de aplicación
4. **Repository** → Accede a datos (solo CRUD)
5. **Entity/Model** → Representa datos
6. **Response** → Retorna vista o JSON

## Gestión de Errores

- Validaciones en FormRequests con mensajes personalizados
- Manejo de excepciones en controladores
- Respuestas consistentes (arrays con 'success' y 'message')

## Testing

- Tests unitarios para casos de uso
- Tests de integración para repositorios
- Tests de feature para controladores

## Configuración del Entorno

- Variables de entorno en `.env`
- Configuración de base de datos
- Configuración de colas y jobs
