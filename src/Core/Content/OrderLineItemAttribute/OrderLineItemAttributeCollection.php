<?php

declare(strict_types=1);

namespace Fyrst\OrderAttributes\Core\Content\OrderLineItemAttribute;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<OrderLineItemAttributeEntity>
 */
class OrderLineItemAttributeCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return OrderLineItemAttributeEntity::class;
    }
}
