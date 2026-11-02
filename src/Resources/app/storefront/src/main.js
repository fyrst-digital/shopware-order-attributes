import OrderAttributesPlugin from './order-attributes';

const PluginManager = window.PluginManager;

// The webpack entry is already named "order-attributes". A dynamic import of
// ./order-attributes produces a second chunk with that same id, which the
// runtime treats as loaded and never fetches. Register the class directly.
PluginManager.register(
    'OrderAttributesPlugin',
    OrderAttributesPlugin,
    '[data-order-attributes]'
);
