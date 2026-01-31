<?php

declare(strict_types=1);

namespace Fyrst\OrderAttributes\Core\Content\OrderLineItemAttribute;

use Fyrst\OrderAttributes\Core\Content\OrderLineItemAttribute\Aggregate\OrderLineItemAttributeTranslation\OrderLineItemAttributeTranslationCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class OrderLineItemAttributeEntity extends Entity
{
    use EntityIdTrait;

    protected string $key;

    protected string $type;

    protected ?array $inputSettings = null;

    protected ?int $position = null;

    protected bool $active = true;

    protected bool $required = false;

    protected ?string $label = null;

    protected ?string $description = null;

    protected ?OrderLineItemAttributeTranslationCollection $translations = null;

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getInputSettings(): ?array
    {
        return $this->inputSettings;
    }

    public function setInputSettings(?array $inputSettings): void
    {
        $this->inputSettings = $inputSettings;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): void
    {
        $this->required = $required;
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

    public function getTranslations(): ?OrderLineItemAttributeTranslationCollection
    {
        return $this->translations;
    }

    public function setTranslations(OrderLineItemAttributeTranslationCollection $translations): void
    {
        $this->translations = $translations;
    }
}
