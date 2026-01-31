<?php

declare(strict_types=1);

namespace Fyrst\OrderAttributes\Core\Content\OrderLineItemAttribute\Aggregate\OrderLineItemAttributeTranslation;

use Fyrst\OrderAttributes\Core\Content\OrderLineItemAttribute\OrderLineItemAttributeDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class OrderLineItemAttributeTranslationDefinition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'fyrst_order_line_item_attribute_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return OrderLineItemAttributeTranslationEntity::class;
    }

    public function getCollectionClass(): string
    {
        return OrderLineItemAttributeTranslationCollection::class;
    }

    protected function getParentDefinitionClass(): string
    {
        return OrderLineItemAttributeDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('label', 'label'))->addFlags(new ApiAware()),
            (new LongTextField('description', 'description'))->addFlags(new ApiAware()),
        ]);
    }
}
