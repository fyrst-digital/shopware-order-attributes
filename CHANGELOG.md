# 1.2.0

**Changelog**
- Added configurable input field size (`Default`, `Small`, `Large`) for order line item attributes in the administration, rendered with Bootstrap size classes (`form-control-sm`, `form-control-lg`) in the storefront
- Added anonymous Twig UX components `OrderAttributes:LineItem:Form` and `OrderAttributes:LineItem:Input` for third-party themes (Shopware 6.7+)
- Removed obsolete comments and rebuilt administration and storefront assets without administration source maps (#14)

# 1.1.1

**Changelog**
- Fixed the custom field indicator icon in the administration order line items grid showing when all custom field values are empty (#13)

# 1.1.0

**Changelog**
- Attached active order attributes on the checkout confirm page (#10)
- Removed leftover debug console logs and updated Composer metadata for Shopware Store validation

# 1.0.0

**Changelog**
- Added configurable order line item attributes in the Shopware administration (**Settings > Plugins > Order Line Item Attributes**) with translatable labels and descriptions, position sorting, active/required toggles, and inline editing (#2)
- Added storefront AJAX auto-save form on cart and offcanvas line items, transferring sanitized attribute values to order line item custom fields on checkout
- Added an indicator icon and detail modal in the administration order line items grid to view saved attribute values
