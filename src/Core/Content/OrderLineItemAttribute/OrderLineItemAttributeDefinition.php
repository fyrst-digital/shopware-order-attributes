<?php

declare(strict_types=1);

namespace Fyrst\OrderAttributes\Core\Content\OrderLineItemAttribute;

use Fyrst\OrderAttributes\Core\Content\OrderLineItemAttribute\Aggregate\OrderLineItemAttributeTranslation\OrderLineItemAttributeTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class OrderLineItemAttributeDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'fyrst_order_line_item_attribute';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return OrderLineItemAttributeEntity::class;
    }

    public function getCollectionClass(): string
    {
        return OrderLineItemAttributeCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new PrimaryKey(), new Required()),
            (new StringField('key', 'key'))->addFlags(new ApiAware(), new Required()),
            (new StringField('type', 'type'))->addFlags(new ApiAware(), new Required()),
            (new JsonField('input_settings', 'inputSettings'))->addFlags(new ApiAware()),
            (new IntField('position', 'position'))->addFlags(new ApiAware()),
            (new BoolField('active', 'active'))->addFlags(new ApiAware()),
            (new BoolField('required', 'required'))->addFlags(new ApiAware()),
            (new TranslatedField('label'))->addFlags(new ApiAware()),
            (new TranslatedField('description'))->addFlags(new ApiAware()),
            (new TranslationsAssociationField(
                OrderLineItemAttributeTranslationDefinition::class,
                'fyrst_order_line_item_attribute_id'
            ))->addFlags(new ApiAware(), new Required()),
        ]);
    }
}
