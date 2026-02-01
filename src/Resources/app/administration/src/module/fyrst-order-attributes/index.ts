import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

const { Module, Locale, Component } = Shopware;

Component.register('fyrst-order-attributes-detail', () => import('./page/fyrst-order-attributes-detail'));
Component.register('fyrst-order-attributes-list', () => import('./page/fyrst-order-attributes-list'));

Module.register('fyrst-order-attributes', {
    type: 'plugin',
    name: 'fyrst-order-attributes.moduleName',
    title: 'fyrst-order-attributes.moduleTitle',
    description: 'fyrst-order-attributes.moduleDescription',
    version: '1.0.0',
    targetVersion: '1.0.0',
    color: '#9AA8B5',
    icon: 'regular-cog',
    entity: 'fyrst_order_line_item_attribute',
    routes: {
        list: {
            component: 'fyrst-order-attributes-list',
            path: 'list',
            meta: {
                parentPath: 'sw.settings.index.plugins',
                privilege: 'fyrst_order_line_item_attribute.viewer'
            }
        },
        create: {
            component: 'fyrst-order-attributes-detail',
            path: 'create',
            meta: {
                parentPath: 'fyrst.order.attributes.list',
                privilege: 'fyrst_order_line_item_attribute.creator'
            }
        },
        detail: {
            component: 'fyrst-order-attributes-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'fyrst.order.attributes.list',
                privilege: 'fyrst_order_line_item_attribute.viewer'
            },
            props: {
                default: (route: { params: { id: string } }) => {
                    return {
                        attributeId: route.params.id
                    };
                }
            }
        }
    },
    // navigation: [
    //     {
    //         id: 'fyrst-order-attributes',
    //         label: 'fyrst-order-attributes.navigationLabel',
    //         path: 'fyrst.order.attributes.list',
    //         parent: 'sw-settings-extension',
    //         privilege: 'fyrst_order_line_item_attribute.viewer',
    //         position: 100
    //     }
    // ]
    /** Module navigation in shopware settings */
    settingsItem: [
        {
            group: 'plugins',
            to: 'fyrst.order.attributes.list',
            iconComponent: 'elysium-icon',
            label: 'fyrst-order-attributes.navigationLabel',
            privilege: 'fyrst_order_line_item_attribute.viewer',
        },
    ],
});

Locale.extend('de-DE', deDE);
Locale.extend('en-GB', enGB);
