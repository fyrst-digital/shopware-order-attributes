<?php

declare(strict_types=1);

namespace Fyrst\OrderAttributes\Subscriber;

use Fyrst\OrderAttributes\Constants;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Checkout\Cart\Event\CartLoadedEvent;
use Shopware\Core\Checkout\Cart\Order\CartConvertedEvent;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\LineItem\LineItemCollection;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;

/**
 * Checkout subscriber for order attributes.
 * 
 * Since cart LineItem only supports payload (not customFields), we store
 * orderAttributes in the cart payload, then move them to customFields
 * during cart-to-order conversion.
 */
class Checkout implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            CartLoadedEvent::class => 'onCartLoaded',
            CartConvertedEvent::class => 'onCartConverted',
        ];
    }

    /**
     * Initialize orderAttributes payload on cart line items if not present.
     * This ensures the payload structure exists for the storefront forms.
     */
    public function onCartLoaded(CartLoadedEvent $event): void
    {
        /** @var Cart $cart */
        $cart = $event->getCart();
        /** @var LineItemCollection<LineItem> $lineItems */
        $lineItems = $cart->getLineItems();

        foreach ($lineItems as $lineItem) {
            if (!$lineItem->hasPayloadValue(Constants::ORDER_ATTRIBUTES_KEY)) {
                $lineItem->setPayloadValue(Constants::ORDER_ATTRIBUTES_KEY, []);
            }
        }
    }

    /**
     * Move orderAttributes from payload to customFields during cart-to-order conversion.
     * This stores the data in the order_line_item.custom_fields column instead of payload.
     */
    public function onCartConverted(CartConvertedEvent $event): void
    {
        $cart = $event->getCart();
        $convertedCart = $event->getConvertedCart();

        foreach ($convertedCart['lineItems'] as $index => $lineItemData) {
            $lineItemId = $lineItemData['identifier'] ?? null;

            if (!$lineItemId) {
                continue;
            }

            $originalLineItem = $cart->getLineItems()->get($lineItemId);

            if (!$originalLineItem || !$originalLineItem->hasPayloadValue(Constants::ORDER_ATTRIBUTES_KEY)) {
                continue;
            }

            $orderAttributes = $originalLineItem->getPayloadValue(Constants::ORDER_ATTRIBUTES_KEY);

            // Skip if orderAttributes is empty
            if (empty($orderAttributes)) {
                continue;
            }

            // Sanitize values before storing
            $orderAttributes = $this->sanitizePayload($orderAttributes);

            // Move to customFields and remove from payload
            $customFields = $convertedCart['lineItems'][$index]['customFields'] ?? [];
            $customFields[Constants::ORDER_ATTRIBUTES_KEY] = $orderAttributes;
            $convertedCart['lineItems'][$index]['customFields'] = $customFields;

            // Remove from payload to avoid duplication
            unset($convertedCart['lineItems'][$index]['payload'][Constants::ORDER_ATTRIBUTES_KEY]);
        }

        $event->setConvertedCart($convertedCart);
    }

    /**
     * Sanitize payload values to prevent XSS attacks.
     * Strips dangerous HTML tags while preserving safe formatting.
     *
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function sanitizePayload(array $payload): array
    {
        $allowedTags = '<p><br><b><strong><i><em><u><ul><ol><li>';

        foreach ($payload as $key => $value) {
            if (is_string($value)) {
                $payload[$key] = strip_tags($value, $allowedTags);
            } elseif (is_array($value)) {
                $payload[$key] = $this->sanitizePayload($value);
            }
        }

        return $payload;
    }
}
