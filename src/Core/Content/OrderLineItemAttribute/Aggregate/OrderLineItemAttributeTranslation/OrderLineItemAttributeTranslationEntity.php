<?php

declare(strict_types=1);

namespace Fyrst\OrderAttributes\Core\Content\OrderLineItemAttribute\Aggregate\OrderLineItemAttributeTranslation;

use Fyrst\OrderAttributes\Core\Content\OrderLineItemAttribute\OrderLineItemAttributeEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;

class OrderLineItemAttributeTranslationEntity extends TranslationEntity
{
    use EntityIdTrait;

    protected string $fyrstOrderLineItemAttributeId;

    protected ?string $label = null;

    protected ?string $description = null;

    protected ?OrderLineItemAttributeEntity $fyrstOrderLineItemAttribute = null;

    public function getFyrstOrderLineItemAttributeId(): string
    {
        return $this->fyrstOrderLineItemAttributeId;
    }

    public function setFyrstOrderLineItemAttributeId(string $fyrstOrderLineItemAttributeId): void
    {
        $this->fyrstOrderLineItemAttributeId = $fyrstOrderLineItemAttributeId;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getFyrstOrderLineItemAttribute(): ?OrderLineItemAttributeEntity
    {
        return $this->fyrstOrderLineItemAttribute;
    }

    public function setFyrstOrderLineItemAttribute(OrderLineItemAttributeEntity $fyrstOrderLineItemAttribute): void
    {
        $this->fyrstOrderLineItemAttribute = $fyrstOrderLineItemAttribute;
    }
}
