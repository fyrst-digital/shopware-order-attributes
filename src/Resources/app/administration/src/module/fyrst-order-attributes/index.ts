import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

const { Module, Locale } = Shopware;

Module.register('fyrst-order-attributes', {
    type: 'core',
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
                parentPath: 'sw.settings.index.extensions',
                privilege: 'fyrst_order_line_item_attribute.viewer'
            }
        },
        create: {
            component: 'fyrst-order-attributes-detail',
            path: 'create',
            meta: {
                parentPath: 'sw.settings.index.extensions',
                privilege: 'fyrst_order_line_item_attribute.creator'
            }
        },
        detail: {
            component: 'fyrst-order-attributes-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'sw.settings.index.extensions',
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
    navigation: [
        {
            id: 'fyrst-order-attributes',
            label: 'fyrst-order-attributes.navigationLabel',
            path: 'fyrst.order.attributes.list',
            parent: 'sw-settings-extension',
            privilege: 'fyrst_order_line_item_attribute.viewer',
            position: 100
        }
    ]
});

Locale.extend('de-DE', deDE);
Locale.extend('en-GB', enGB);
