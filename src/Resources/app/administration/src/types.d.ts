import type { ShopwareClass } from 'src/core/shopware';
import type EntityCollectionClass from 'src/core/data/entity-collection.data';
import '@shopware-ag/entity-schema-types';

declare global {
    const Shopware: ShopwareClass;

    interface Window {
        Shopware: ShopwareClass;
    }

    type Entity<EntityName extends keyof EntitySchema.Entities> = EntitySchema.Entities[EntityName];
    type EntityCollection<EntityName extends keyof EntitySchema.Entities> = EntityCollectionClass<EntityName>;
}

declare global {
    namespace EntitySchema {
        interface Entities {
            fyrst_order_line_item_attribute: fyrst_order_line_item_attribute;
        }
    }
}

interface fyrst_order_line_item_attribute {
    id: string;
    key: string;
    label: string;
    type: string;
    inputSettings: Record<string, unknown> | null;
    position: number;
    active: boolean;
    required: boolean;
    description: string | null;
    customFields: unknown;
    createdAt: string;
    updatedAt: string | null;
    translations: EntityCollection<'fyrst_order_line_item_attribute_translation'>;
}
