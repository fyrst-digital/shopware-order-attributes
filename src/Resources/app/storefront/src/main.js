const PluginManager = window.PluginManager;
console.log('fdsgsd')
PluginManager.register(
    'OrderAttributesPlugin',
    () => import('./order-attributes'),
    '[data-order-lineitems-attributes]'
);
