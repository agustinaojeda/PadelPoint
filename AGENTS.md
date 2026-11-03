# PadelPoint

Laravel 12 + Inertia v2 + Vue 3/TypeScript app for padel-court bookings and an admin panel. Domain names, UI copy, and validation messages are Spanish (`Cancha`, `Reserva`, `turno`) — keep new ones consistent.

The auth/settings scaffolding comes from the Laravel Vue starter kit; app-specific logic is the public `home` page and the authenticated `canchas`, `reservas`, and `dashboard` modules.

## Commands

- Full dev stack: `composer run dev` (Artisan serve + queue listener + Pail + Vite).
- Tests: `php artisan test --compact`; one file: `php artisan test --compact tests/Feature/ReservaTest.php`; one test: add `--filter="test name"`. Direct runner: `vendor/bin/pest`.
- PHP format (required after PHP edits): `vendor/bin/pint --dirty --format agent`. There is no `pint.json`; the default Laravel preset applies.
- Frontend: `npm run dev`, `npm run build`, `npm run lint` (ESLint, applies fixes), `npm run format` / `format:check` (Prettier, writes only `resources/`).
- No typecheck npm script. Use `npx vue-tsc --noEmit`; it currently reports two pre-existing errors at `resources/js/composables/useCanchaForm.ts:23` and `resources/js/pages/reservas/components/ModalReserva.vue:22`. Compare against that baseline instead of fixing unrelated code.
- If `npm`/`npx` fail with a PowerShell execution-policy error, use `cmd /c "npm run ..."`.

## Architecture

- Admins authenticate as `App\Models\Admin`, not Laravel's `User` (guard/provider `admins`, credential columns `correoAdmin` / `contrasenaAdmin`). Tests use `Admin::factory()` with `actingAs($admin)`.
- Controllers are thin. Mutations live in invokable classes under `app/<Domain>/Actions`; read/aggregate logic in `app/<Domain>/Queries`. Constructor-inject them and invoke as `($this->action)(...)`.
- All HTTP routes are web/Inertia (`routes/web.php`, `routes/auth.php`, `routes/settings.php`); there is no API layer. Pages are `resources/js/pages/<area>/index.vue`, with page-local components in `pages/<area>/components/`; `@/` maps to `resources/js`.
- Frontend: shadcn-vue components in `@/components/ui`, icons from `lucide-vue-next`, toasts via `vue-sonner` + `useFeedback()`, and Ziggy's `route()` is globally available in Vue.

## Domain rules

- Reservation states: `pendiente`, `confirmada`, `cancelada`, `completada` (`App\Enums\EstadoReserva`). A completed reservation cannot change state or be deleted.
- Double-booking is prevented server-side in `CreateReserva` + `ExisteSolapamientoReserva`: every state except `cancelada` blocks an overlapping slot, checked inside a `lockForUpdate` transaction. `resources/js/lib/turnos.ts` only mirrors this for display, so always enforce on the server.
- `DELETE /canchas/{cancha}` deactivates (`esta_disponible = false`, `desactivada_en = now`); `DELETE /canchas/{cancha}/eliminar` hard-deletes and is rejected while reservas exist. A scheduled command purges canchas deactivated more than 30 days ago with no reservas.
- Cancha images go through `App\Cancha\Support\CanchaImagen`: `public` disk, DB path `storage/canchas/...`, fallback `images/cancha-default.webp`. New setups need `php artisan storage:link`.
- `routes/console.php` schedules `app:completar-reservas-vencidas` hourly and `app:purge-desactivadas-canchas` daily; run `php artisan schedule:work` locally if you need them.

## Database and tests

- Dev DB is MySQL (`padelpoint`); tests use in-memory SQLite (`phpunit.xml`), with `RefreshDatabase` applied to every Feature test in `tests/Pest.php`.
- Raw SQL must work on both drivers. `GetOcupacionPorDia` and `GetBalanceAnual` switch between SQLite `strftime` and MySQL `DAYOFWEEK`/`MONTH`; add both branches for new date-based SQL.
- Timezone `America/Argentina/Buenos_Aires`, locale `es`, Faker `es_ES`. Seeded admin: `admin@padelpoint.com` / `admin123`.
- Pest style: `test('...')` names, Feature tests under `tests/Feature`, and factories with states such as `completada()`, `cancelada()`, and `desactivada()`.
- Prettier uses 4 spaces, 150 columns, single quotes, organized imports, and sorted Tailwind classes. Vue components need a single root element. If `format:check` flags untouched files, format only the files you changed.

## Conventions

- Create files with `php artisan make:* --no-interaction`; do not add verification scripts when a Pest test covers the behavior.
- PHP: explicit parameter and return types, braces on every control structure, constructor property promotion, and `casts()` methods (match the existing models).

## Working agreements

- Add or update a test for every behavior change; run the narrowest affected test file.
- Do not delete tests or change dependencies without approval, and do not create documentation files unless asked.
- Read the relevant `.github/skills/<name>/SKILL.md` (Laravel, testing, Inertia/Vue, Tailwind, convention inference) before large changes.
- Laravel Boost is installed (`boost.json`); run `php artisan boost:update` if you need its generated Laravel guidelines.

===

## Foundational Context

This application is a Laravel application running on PHP 8.5. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If a frontend change doesn't show in the UI or you get a "Unable to locate file in Vite manifest" error, run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists, including path-scoped framework guidelines under `.ai/rules/boost`. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Add or update tests for behavior and logic changes when a test provides meaningful regression coverage.
- Pure copy, styling, and layout-only changes do not require new or updated tests.
- When test coverage applies, run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.
- IMPORTANT: Activate `inertia-vue-development` when working with Inertia Vue client-side patterns.

# Inertia v2

- Use all Inertia features from v1 and v2. Check the documentation before making changes to ensure the correct approach.
- New features: deferred props, infinite scroll, merging props, polling, prefetching, once props, flash data.
- When using deferred props, add an empty state with a pulsing or animated skeleton.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- This project uses the streamlined Laravel 11+ structure: register middleware, exceptions, and routing in `bootstrap/app.php` and service providers in `bootstrap/providers.php`. There is no `app/Http/Kernel.php` or `app/Console/Kernel.php`, and commands in `app/Console/Commands/` auto-register.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.

- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

# Pest

- This project uses Pest. Create tests with `php artisan make:test --pest {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.
- Do not delete tests or test files without approval. They are part of the application.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/pest` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.

=== inertia-vue/core rules ===

# Inertia + Vue

Vue components must have a single root element.
- IMPORTANT: Activate `inertia-vue-development` when working with Inertia Vue client-side patterns.

</laravel-boost-guidelines>
