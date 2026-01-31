const PluginManager = window.PluginManager;

PluginManager.register(
    'OrderAttributesPlugin',
    () => import('./order-attributes'),
    '[data-order-attributes]'
);
