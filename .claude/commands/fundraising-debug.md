# fundraising-debug

Eres un experto en el módulo Fundraising de este proyecto. Antes de responder cualquier pregunta sobre bugs, lógica o comportamiento, lee los siguientes archivos clave:

- `app/Modules/Fundraising/Application/UseCases/ApplyDailyPenalties.php`
- `app/Modules/Fundraising/Application/UseCases/SyncChargesWithTransactions.php`
- `app/Modules/Fundraising/Application/UseCases/CreateMonthlyCharges.php`
- `app/Modules/Fundraising/Infrastructure/Persistence/EloquentFundraisingChargeRepository.php`
- `app/Modules/Fundraising/Presentation/Controllers/FundraisingApiController.php`
- `resources/views/modules/fundraising/recaudaciones.blade.php`

Luego responde con base en este contexto de negocio:

## Reglas de negocio Fundraising

### Cobro mensual
- Se genera un cargo de **$1.00 por usuario** el día 15 de cada mes (`charge_date = 15`)
- Solo se crea si no existe ya un cargo para ese usuario/tipo/mes

### Mora
- **$0.05 por día** por cada día que el cargo base esté sin pagar
- La mora NO es acumulativa entre meses — siempre es $0.05/día sin importar cuántos meses lleve impago
- La mora **se congela** cuando `paid_amount >= base_amount` (el base está cubierto)
- Una vez congelada, sigue siendo una deuda pendiente pero ya no crece
- Para liquidar completamente: hay que pagar `base_amount + penalty_amount`

### Pagos
- Los pagos siempre entran a la tabla `transactions` (módulo Transaction), nunca directo a `fundraising_charges`
- `SyncChargesWithTransactions` recalcula `paid_amount` e `is_fully_paid` usando el balance de transacciones
- Orden de pago FIFO: se pagan los cargos más antiguos primero

### Flujo de run-manual (botón "Ejecutar cobro manual")
Orden estricto de ejecución:
1. `CreateMonthlyCharges` — crea el cargo del mes si no existe
2. `SyncChargesWithTransactions` — sincroniza pagos desde transacciones
3. `ApplyDailyPenalties` — aplica $0.05 × días_perdidos donde `paid_amount < base_amount`

### Schema tabla `fundraising_charges`
| Columna | Descripción |
|---|---|
| `base_amount` | $1.00 — monto del cargo |
| `penalty_amount` | mora acumulada (se congela cuando base está cubierto) |
| `paid_amount` | calculado por SyncChargesWithTransactions |
| `charge_date` | día 15 del mes |
| `penalty_last_applied_date` | evita aplicar mora dos veces el mismo día |
| `is_fully_paid` | true cuando paid_amount >= base + penalty |

### Bug conocido y resuelto (2026-04)
**Síntoma**: alguien paga el base pero la mora sigue creciendo en cada run-manual.
**Causa**: `ApplyDailyPenalties` solo verificaba `is_fully_paid = false`, no si el base estaba cubierto.
**Fix aplicado**: `findUnpaidOlderThan()` en el repositorio incluye `whereColumn('paid_amount', '<', 'base_amount')`.

---

Ahora analiza el problema o pregunta del usuario con este contexto. Si es un bug, traza el flujo exacto paso a paso antes de proponer solución.
