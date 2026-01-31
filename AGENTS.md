# AGENTS.md - Development Guidelines

This is a **Shopware 6 Platform Plugin** that adds configurable order line item attributes functionality.

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
