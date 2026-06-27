<?php

declare(strict_types=1);

namespace Fyrst\OrderAttributes\Core\Content\OrderLineItemAttribute\Aggregate\OrderLineItemAttributeTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<OrderLineItemAttributeTranslationEntity>
 */
class OrderLineItemAttributeTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return OrderLineItemAttributeTranslationEntity::class;
    }
}
