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
- **Use Cases**: CreateMonthlyCharges, ApplyDailyPenalties, SyncChargesWithTransactions, GetChargesByType, GetUserCharges
- **Job**: ProcessDailyFundraisingJob (runs daily at 00:00)
- **Controller**: FundraisingApiController — `POST /api/fundraising/run-manual`, `DELETE /api/fundraising/reset-data`
- **View**: `resources/views/modules/fundraising/recaudaciones.blade.php` (single unified view)

#### Fundraising Business Rules (CRITICAL)
1. **Monthly charge**: $1.00 per user, generated on the 15th of each month (`charge_date = 15th`).
2. **Daily penalty**: $0.05/day for each day the base charge is **unpaid** (not covered by transactions).
3. **Penalty is NOT cumulative across months** — it's always $0.05/day, regardless of how many months unpaid.
4. **Penalty STOPS accruing once `paid_amount >= base_amount`** (base covered). The accumulated penalty becomes a frozen debt — it no longer grows. Full settlement requires paying base + frozen penalty.
5. **Payments flow through transactions** (Transaction module). Payments never go directly into `fundraising_charges.paid_amount` — always via `SyncChargesWithTransactions`.
6. **FIFO payment allocation**: `SyncChargesWithTransactions` applies the user's transaction balance to charges oldest-first.

#### fundraising_charges table schema
| Column | Type | Description |
|---|---|---|
| `user_id` | FK | User |
| `type` | string | e.g. 'navidad' |
| `base_amount` | decimal | $1.00 per charge |
| `penalty_amount` | decimal | Accumulated penalty (frozen once base is paid) |
| `paid_amount` | decimal | Set by SyncChargesWithTransactions |
| `charge_date` | date | Always the 15th of the month |
| `penalty_last_applied_date` | date\|null | Last date penalty was applied (prevents double-applying same day) |
| `is_fully_paid` | bool | True when paid_amount >= base_amount + penalty_amount |

#### run-manual execution order (FundraisingApiController::runManual)
1. `CreateMonthlyCharges` — creates charge for today's month if not already exists
2. `SyncChargesWithTransactions` — recalculates paid_amount + is_fully_paid from transaction balances
3. `ApplyDailyPenalties` — adds $0.05 × missed_days to charges where `paid_amount < base_amount`

#### Key penalty bug fixed (2026-04)
**Problem**: After a user paid the base ($1.00) but not the accrued penalty, mora kept growing indefinitely.
**Root cause**: `ApplyDailyPenalties` only checked `is_fully_paid = false`, not whether the base was covered.
**Fix**: `findUnpaidOlderThan()` in `EloquentFundraisingChargeRepository` now includes `whereColumn('paid_amount', '<', 'base_amount')`. Penalty only accrues while base is uncovered.

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

### Shared / Configuration
- **Table**: `configurations` — columns: `id`, `variable` (unique), `value`, `timestamps`
- **Repository Interface**: `Shared/Domain/Repositories/ConfigurationRepositoryInterface` — `all()`, `get()`, `set()`
- **Repository Implementation**: `Shared/Infrastructure/Persistence/EloquentConfigurationRepository`
- **Model**: `Shared/Infrastructure/Persistence/Models/ConfigurationModel`
- **Service**: `Shared/Domain/Services/ConfigurationService` — **singleton**, loads all rows once per request (in-memory cache). Use `$service->get('key', $default)` and `$service->all()`.
- **View Composer**: `Shared/Presentation/ViewComposers/GlobalConfigComposer` — registered for `*` (all views) in `ModuleServiceProvider::boot()`.
- **Variables available in every Blade view** (no need to pass from controller):
  - `$appName` — value of `app_name` row
  - `$appDescription` — value of `app_description` row
  - `$appConfig` — full `['variable' => 'value']` array for any custom key
- **Adding a new global variable**: insert a row in `configurations` table, then read it in views via `$appConfig['my_key']` or inject `ConfigurationService` in any class.
- **Rule**: NEVER hardcode the app name or global UI strings in views/layouts — always use `$appName` / `$appConfig`.

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
