# AGENTS.md - Development Guidelines

This is a **Shopware 6 Platform Plugin** that adds configurable order line item attributes functionality.

## Bun as javascript runtime
For the administration and storefront app we use `bun` as javascript runtime and package manager instead `npm`

## Build/Test Commands

Since this is a Shopware plugin, use the host Shopware installation's tooling:

### Install dependencies (from host Shopware root)
```bash
composer install
```
If the symfony CLI is available, use:
```bash
symfony composer install
```

### Run PHP tests from host (if configured)
```bash
./vendor/bin/phpunit --filter OrderAttributes
```

### Run static analysis from host
```bash
./vendor/bin/phpstan analyse custom/plugins/FyrstOrderAttributes/src
./vendor/bin/psalm custom/plugins/FyrstOrderAttributes/src
```

### Run code style checks from host
```bash
./vendor/bin/php-cs-fixer fix custom/plugins/FyrstOrderAttributes/src --dry-run
```

### Build admin/storefront assets (from host Shopware root)
```bash
bin/build-storefront.sh 
bin/build-administration.sh 
```
If the symfony CLI is available, use:
```bash
symfony run bin/build-storefront.sh 
symfony run bin/build-administration.sh 
```

## Code Style Guidelines

### PHP

- **Strict typing**: Always use `declare(strict_types=1);` at the start of PHP files
- **PSR-4 autoloading**: Namespace `Fyrst\OrderAttributes\` maps to `src/`
- **Class naming**: PascalCase (e.g., `OrderLineItemAttributeEntity`)
- **Method/property naming**: camelCase (e.g., `getEntityName()`, `$inputSettings`)
- **Constants**: UPPER_SNAKE_CASE for entity names (e.g., `ENTITY_NAME = 'fyrst_order_line_item_attribute'`)
- **Type hints**: Use proper type hints and return types for all methods
- **Constructor promotion**: Use PHP 8 constructor property promotion
- **Docblocks**: Include docblocks for complex types and array generics (e.g., `@param array<string, mixed> $payload`)
- **Imports**: Group imports: 1) Vendor, 2) Shopware, 3) Local; alphabetically within groups
- **Entity pattern**: Follow Shopware DAL - Entity, Definition, Collection classes

### JavaScript

- **Imports**: Use ES6 import syntax (e.g., `import Plugin from 'src/plugin-system/plugin.class'`)
- **Class naming**: PascalCase extending Shopware base classes
- **Method naming**: camelCase, private methods prefixed with `_` (e.g., `_onSubmit()`)
- **Shopware globals**: Access via `const { Component, Locale } = Shopware`

### Templates (Twig)

- **Block naming**: snake_case with component prefix (e.g., `{% block component_line_item_order_attributes_form %}`)
- **Variable naming**: camelCase in logic, snake_case for translation keys
- **Escaping**: Use `|e('html_attr')` for JSON in HTML attributes
- **Form handling**: Use `data-form-auto-submit` for AJAX forms

### Styles (SCSS)

- **BEM-like naming**: `.sw-order-line-items-grid__item-product`
- **Spacing**: Use Bootstrap utilities (e.g., `py-3`, `gap-8`)

### Database/Migrations

- **Migration naming**: `Migration{timestamp}{Description}`
- **Table naming**: snake_case with vendor prefix (e.g., `fyrst_order_line_item_attribute`)
- **Foreign keys**: Use explicit constraint names with fk.{table}.{field} pattern
- **Timestamps**: Include `created_at` DATETIME(3) and `updated_at` DATETIME(3) NULL

### Services & Configuration

- **DI**: Use `services.xml` for service registration
- **Entity tags**: Tag entity definitions with `shopware.entity.definition`
- **Routes**: Define in `routes.xml`, use Symfony attributes in controllers
- **CSRF**: Disable for AJAX routes with `defaults: ['csrf_protected' => false]`

## Error Handling

- **Exceptions**: Catch specific exceptions, provide fallback responses for storefront
- **Validation**: Flash messages for storefront errors via `$this->addFlash()`
- **XSS Prevention**: Sanitize user input with `strip_tags()` using allowed HTML whitelist

## Cursor Cloud specific instructions

This repo is **only the plugin** (`Fyrst\OrderAttributes`). To run/build it you need a host Shopware
installation. The cloud VM snapshot already contains a working host install — these notes explain how it is
wired and how to operate it. The update script only refreshes the plugin's root npm deps (`npm install`);
everything below is durable context, not one-off setup.

### Host Shopware install layout
- Host Shopware **6.7.x** lives at `/home/ubuntu/shopware` (the plugin's `composer.json` allows `^6.6`, which
  resolves to the latest 6.7 line).
- The plugin is symlinked into the host: `/home/ubuntu/shopware/custom/plugins/OrderAttributes -> /workspace`.
  Edits to repo source are reflected in the host immediately through this symlink (no copy step).
- DB: MariaDB, database `shopware`, user/pass `shopware`/`shopware` over TCP `127.0.0.1:3306`
  (`DATABASE_URL` is set in `/home/ubuntu/shopware/.env`). Host install runs in **dev** mode via
  `/home/ubuntu/shopware/.env.local` (`APP_ENV=dev`). Admin login: `admin` / `shopware`.
- Tooling installed: PHP 8.3 (CLI), Composer, Bun (`~/.bun/bin`), Symfony CLI, `shopware-cli`, MariaDB,
  Node/npm. `shopware/dev-tools` is installed in the host (enables `framework:demodata` and dev profiler).

### Starting services (NOT done by the update script — do this each session)
- Start the DB: `sudo service mariadb start`
- Start the web server (from `/home/ubuntu/shopware`): `symfony server:start --no-tls --port=8000 --allow-all-ip`
  Storefront: `http://127.0.0.1:8000/` · Admin: `http://127.0.0.1:8000/admin`
- Demo products already exist. If a fresh shop is ever needed, re-run
  `bin/console framework:demodata --products=10 --categories=3 --manufacturers=3` then `bin/console dal:refresh:index`.

### Building assets after changing plugin JS/TS/SCSS/Twig (from `/home/ubuntu/shopware`)
- Admin (Vite): `bash bin/build-administration.sh` — needs `jq` (installed) and runs `bin/console bundle:dump` first.
- Storefront (Webpack): `bash bin/build-storefront.sh`
- After PHP/`services.xml`/route changes, run `bin/console cache:clear`.
- **Gotcha:** the plugin commits its compiled output (`src/Resources/app/storefront/dist/**` and
  `src/Resources/public/administration/**`). Running the host build regenerates and thus *modifies* these
  committed files. Don't accidentally commit those regenerated artifacts unless your change intends to update them.

### Lint / validate / test (from `/workspace` unless noted)
- Admin lint works: `npm run lint:administration`.
- **`npm run lint:storefront` is broken by the repo's own `.eslintrc.js`** — it forces every file through the
  administration `tsconfig.json` (typed linting), which does not include the storefront `.js` files, so ESLint
  errors out with "TSConfig does not include this file". This is a pre-existing config issue, not an env problem.
- CI check (matches `.github/workflows/ci.yml`): `shopware-cli extension validate .` (passes; only emits
  xml-vs-yaml deprecation warnings).
- There are no PHPUnit tests bundled in the plugin, and phpstan/psalm/php-cs-fixer are not installed (they are
  not declared as plugin dependencies); the `AGENTS.md` build commands above describe host tooling that would
  need to be added separately if you want them.
