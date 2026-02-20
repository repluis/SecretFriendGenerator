# SecretFriendGenerator - Architecture Rules

## Stack
- Laravel 12 (PHP 8.x)
- Blade views with Vite (CSS/JS)
- SQLite database
- Windows 11 environment

## Architecture: Modular DDD

```
app/Modules/
├── SecretSanta/       # Secret friend game module
├── Fundraising/       # Monthly charges & penalties module
├── Transaction/       # Credit/debit transactions module
├── User/              # User management module
└── Shared/            # Shared interfaces & providers
```

Each module follows 4 layers:

```
Domain/          → Entities (pure PHP), Repository Interfaces, Services
Application/     → Use Cases (business logic orchestration)
Infrastructure/  → Eloquent Models, Eloquent Repository implementations
Presentation/    → Controllers (thin, delegates to use cases)
```

## Data Flow (STRICT)

```
Controller → UseCase → RepositoryInterface → EloquentRepository → Database
```

## CRITICAL RULES

### 1. NO direct model access outside Infrastructure
- Controllers and Use Cases must NEVER use Eloquent models directly
- `User::`, `PlayerModel::`, `UrlModel::`, etc. are ONLY allowed inside `Infrastructure/Persistence/` files
- Always use Repository Interfaces injected via constructor

### 2. Use Cases pattern
- Every use case implements `UseCaseInterface` with `execute(array $params = []): mixed`
- Inject repository interfaces via constructor (`__construct(private RepoInterface $repo)`)
- Use cases contain business logic, NOT controllers

### 3. Repository pattern
- Interfaces live in `Domain/Repositories/`
- Implementations live in `Infrastructure/Persistence/`
- Bindings registered in `Shared/Infrastructure/Providers/ModuleServiceProvider.php`
- Repositories that need to return Eloquent models for views (with relations) use specific methods like `getActiveModelsOrderedByName()`, `findAllWithRelations()`

### 4. Controllers are thin
- Controllers only: validate request, call use case, return response
- Inject use cases via method injection or constructor
- API controllers return `response()->json([...])`
- Web controllers return `view('name', compact(...))`

## Key Files

| Purpose | File |
|---|---|
| Service Provider (bindings) | `app/Modules/Shared/Infrastructure/Providers/ModuleServiceProvider.php` |
| UseCase Interface | `app/Modules/Shared/Domain/UseCaseInterface.php` |
| API Routes | `routes/api.php` |
| Web Routes | `routes/web.php` |
| Scheduler | `routes/console.php` |
| Dashboard view | `resources/views/modules/dashboard/index.blade.php` |

## Module Details

### SecretSanta
- **Entities**: Player, SecretFriendUrl, GameConfiguration
- **Repositories**: PlayerRepositoryInterface, UrlRepositoryInterface, GameConfigurationRepositoryInterface
- **Use Cases**: Player/ (7), Url/ (6), Friend/ (3), View/ (4), Game/ (3)
- **Controllers**: PlayerApiController, GameConfigApiController, SecretFriendViewController, HomeController

### Fundraising
- **Entity**: FundraisingCharge
- **Repository**: FundraisingChargeRepositoryInterface
- **Use Cases**: CreateMonthlyCharges, ApplyDailyPenalties, GetChargesByType
- **Job**: ProcessDailyFundraisingJob (runs daily at 00:00)
- **Logic**: Every 15th = $1.00 charge per user. Daily $0.05 penalty on unpaid charges.

### Transaction
- **Entity**: Transaction
- **Repository**: TransactionRepositoryInterface
- **Use Cases**: CreateTransaction, ToggleTransactionStatus, GetAllTransactions, GetUserBalances
- **Controller**: TransactionApiController
- **Logic**: Credit transactions add to balance, debit subtract. Only active transactions count. Used for all balance calculations (dashboard + recaudaciones).

### User
- **Entity**: UserEntity
- **Repository**: UserRepositoryInterface
- **Use Cases**: GetAllUsers, CreateUser, UpdateUser, DeactivateUser (toggle active)
- **Controller**: UserApiController

## Routes

### Web
- `/` → Dashboard (HomeController@index)
- `/juego` → Secret Santa game (HomeController@game)
- `/configuracion` → Game configuration (HomeController@configuracion)
- `/secret-friend/{url}` → View secret friend
- `/fundraising/navidad` → Christmas fundraising
- `/fundraising/recaudaciones` → Collection details

### API (prefix: /api)
- `/players/*` → Player CRUD + game operations
- `/game-config/*` → Game configuration
- `/users/*` → User CRUD
- `/transactions` → GET (list), POST (create)
- `/transactions/{id}/toggle-active` → PATCH (toggle status)

## Frontend Architecture

### Layouts (`resources/views/layouts/`)
- **`app.blade.php`** — Admin theme (Inter font, indigo #6366f1). Used by dashboard, fundraising views. Includes navbar, common CSS (buttons, tables, badges, forms, modals), and `csrfToken` JS constant.
- **`christmas.blade.php`** — Christmas theme (Mountains of Christmas font, red/gold). Used by secret-santa game views. Snowflakes, festive styling. No navbar.
- **`message.blade.php`** — Simple gradient message pages. Used by error/status views (game-not-started, already-viewed, etc.). Yields: `gradient-from`, `gradient-to`.
- **`partials/navbar.blade.php`** — Admin navbar partial. Receives `$active` param for active link highlighting.

### Components (`resources/views/components/`)
Anonymous Blade components used as `<x-component-name>`:

| Component | Props | Usage |
|---|---|---|
| `stat-card` | `$label, $value, $color, $detail` | Dashboard stat cards |
| `badge` | `$color` + slot | Status badges (green, red, amber, blue, slate) |
| `avatar` | `$name` | User avatar with initials |
| `modal` | `$id, $title` + slot | Modal dialogs |
| `card` | `$class` + slot | Generic white card container |
| `empty-state` | `$message, $submessage` | Empty table/list state |
| `table.table` | `$headers, $title` + slot + `$toolbar` slot | Data tables with optional toolbar |
| `form.input` | `$id, $label, $type, $placeholder, $required, $value` | Form inputs |
| `form.button` | `$variant, $type` + slot | Buttons (primary, cancel, danger, ghost, success) |

### Module Views (`resources/views/modules/`)
```
modules/
├── dashboard/
│   └── index.blade.php           ← @extends('layouts.app')
├── fundraising/
│   ├── navidad.blade.php         ← @extends('layouts.app')
│   └── recaudaciones.blade.php   ← @extends('layouts.app')
└── secret-santa/
    ├── index.blade.php           ← @extends('layouts.christmas')
    ├── configuracion.blade.php   ← @extends('layouts.christmas')
    ├── admin.blade.php           ← @extends('layouts.christmas')
    ├── secret-friend.blade.php   ← @extends('layouts.message')
    ├── game-not-started.blade.php ← @extends('layouts.message')
    ├── already-viewed.blade.php  ← @extends('layouts.message')
    └── no-friend-assigned.blade.php ← @extends('layouts.message')
```

### Frontend Rules
1. **All views must extend a layout** — never duplicate DOCTYPE/head/body
2. **Common CSS lives in layouts** — page-specific CSS goes in `@section('styles')`
3. **Use components** for repeated UI patterns (tables, badges, stat cards, forms)
4. **View paths in controllers** use dot notation: `modules.dashboard.index`, `modules.secret-santa.index`
5. **Admin views** use `layouts.app`, **Christmas views** use `layouts.christmas`, **message pages** use `layouts.message`
6. **Navbar active state** set via `$navbarActive` variable passed from controller or set in view

## Language
- Code: English
- UI text / views: Spanish
- Database columns: Spanish for legacy tables (players: nombre, estado), English for new tables (fundraising_charges, users)
