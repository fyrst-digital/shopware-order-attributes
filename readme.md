# OrderAttributes for Shopware 6

Configurable per-line-item attributes for the cart and checkout flow.

| | |
|---|---|
| **Package** | `fyrst/shopware-order-attributes` |
| **Version** | 1.0.0 |
| **Shopware** | ^6.6 |
| **License** | MIT |

## Overview

OrderAttributes enables merchants to define custom input fields — such as commission numbers, customer references, or delivery notes — that customers complete on each cart line item during the shopping process. Attribute values are persisted to the corresponding order line items upon checkout, ensuring they remain accessible throughout order management and fulfilment.

## Features

- Configurable attributes managed entirely through the Shopware administration
- Translatable labels and descriptions with full multi-language support
- Storefront AJAX form on each cart line item with auto-save (no page reload required)
- Attribute values stored as custom fields on order line items after checkout
- Dedicated modal in the order detail grid for viewing saved attribute values
- Inline editing of position, active, and required flags in the attribute list
- XSS sanitization applied to all user-supplied values

## Requirements

- Shopware 6.6.x
- PHP 8.1 or later

## Installation

### Via Composer

```bash
composer require fyrst/shopware-order-attributes
bin/console plugin:install --activate OrderAttributes
```

### Via Administration

1. Navigate to **Settings > System > Plugins**.
2. Upload or locate the plugin in the store.
3. Click **Install**, then **Activate**.

## Usage

### Managing Attributes

Navigate to **Settings > Plugins > Order Line Item Attributes** to create and manage attributes.

Each attribute consists of the following fields:

| Field | Description |
|---|---|
| **Key** | Unique identifier for the attribute. Immutable after creation. |
| **Type** | Input field type. Currently supports *Text*. |
| **Label** | Display label shown to the customer. Translatable. |
| **Description** | Optional help text. Translatable. |
| **Position** | Numeric sort order when rendering multiple attributes. |
| **Active** | Toggle to show or hide the attribute in the storefront. |
| **Required** | Toggle to mark the attribute as mandatory. |

The list view supports inline editing for the **Position**, **Active**, and **Required** columns.

### Storefront Experience

When active attributes exist, each cart line item displays the corresponding input fields directly below the product name. Values are saved automatically via AJAX when the customer changes a field or presses Enter — no page reload occurs. A loading indicator provides visual feedback during the save request.

The same form is also available in the offcanvas (slide-out) cart.

### Order Processing

During checkout, attribute values are transferred from the cart payload to the order line item's custom fields. In the administration order detail view, a small indicator icon appears next to line items that contain saved attributes. Clicking the icon opens a modal displaying each attribute name alongside its stored value.

## Architecture

| Component | Location |
|---|---|
| Entity definition | `src/Core/Content/OrderLineItemAttribute/` |
| Entity tables | `fyrst_order_line_item_attribute`, `fyrst_order_line_item_attribute_translation` |
| Event subscriber | `src/Subscriber/Checkout.php` |
| AJAX controller | `src/Controller/OrderAttributesController.php` (`POST /order-attributes/add`) |
| Admin module | `src/Resources/app/administration/src/module/fyrst-order-attributes/` |
| Admin order grid override | `src/Resources/app/administration/src/override/sw-order-line-items-grid/` |
| Storefront JS plugin | `src/Resources/app/storefront/src/order-attributes.js` |
| Twig templates | `src/Resources/views/storefront/component/line-item/` |

The subscriber hooks into four Shopware events:

- `CartLoadedEvent` — initialises the order attributes payload structure on each line item
- `CartConvertedEvent` — migrates attribute values from payload to custom fields during checkout
- `CheckoutCartPageLoadedEvent` — attaches active attributes to the checkout cart page
- `OffcanvasCartPageLoadedEvent` — attaches active attributes to the offcanvas cart page

## Customisation

The storefront form template exposes the following Twig blocks for extension:

| Block | Purpose |
|---|---|
| `component_line_item_type_product_order_attributes` | Wrapper block injected into the product line item template |
| `component_order_attributes_line_item_form_prepend` | Rendered before the form inputs |
| `component_order_attributes_line_item_form_input` | The attribute input fields |
| `component_order_attributes_line_item_form_append` | Rendered after the form inputs |

## Building Assets

Asset compilation uses `bun` as the JavaScript runtime and package manager. Run the following from the Shopware root directory:

```bash
# Administration (Vite)
bin/build-administration.sh

# Storefront (Webpack)
bin/build-storefront.sh
```

If the Symfony CLI is available:

```bash
symfony run bin/build-administration.sh
symfony run bin/build-storefront.sh
```

## License

This plugin is released under the [MIT License](https://opensource.org/licenses/MIT).
