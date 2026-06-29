<?php

declare(strict_types=1);

namespace Fyrst\OrderAttributes\Subscriber;

use Fyrst\OrderAttributes\Constants;
use Shopware\Core\Checkout\Cart\Event\CartLoadedEvent;
use Shopware\Core\Checkout\Cart\Order\CartConvertedEvent;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\LineItem\LineItemCollection;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Storefront\Page\Checkout\Cart\CheckoutCartPageLoadedEvent;
use Shopware\Storefront\Page\Checkout\Confirm\CheckoutConfirmPageLoadedEvent;
use Shopware\Storefront\Page\Checkout\Offcanvas\OffcanvasCartPageLoadedEvent;
use Shopware\Storefront\Page\PageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Checkout subscriber for order attributes.
 *
 * Since cart LineItem only supports payload (not customFields), we store
 * orderAttributes in the cart payload, then move them to customFields
 * during cart-to-order conversion.
 */
class Checkout implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityRepository $repository,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            CartLoadedEvent::class => 'onCartLoaded',
            CartConvertedEvent::class => 'onCartConverted',
            CheckoutCartPageLoadedEvent::class => ['addOrderAttributesToPage', 0],
            CheckoutConfirmPageLoadedEvent::class => ['addOrderAttributesToPage', 0],
            OffcanvasCartPageLoadedEvent::class => ['addOrderAttributesToPage', 0],
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
            unset($convertedCart['lineItems'][$index]['payload'][Constants::ORDER_ATTRIBUTES_KEY]);

            if (!is_array($orderAttributes)) {
                continue;
            }

            $orderAttributes = $this->removeEmptyValues($this->sanitizePayload($orderAttributes));

            if (empty($orderAttributes)) {
                continue;
            }

            // Move to customFields and remove from payload
            $customFields = $convertedCart['lineItems'][$index]['customFields'] ?? [];
            $convertedCart['lineItems'][$index]['customFields'] = $this->setOrderAttributes($orderAttributes, $customFields);
        }

        $event->setConvertedCart($convertedCart);
    }

    private function setOrderAttributes(array $orderAttributes, mixed $customFields): mixed
    {
        foreach ($orderAttributes as $key => $value) {
            $customFields[$key] = $value;
        }
        return $customFields;
    }

    public function addOrderAttributesToPage(PageLoadedEvent $event): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->addSorting(new FieldSorting('position', FieldSorting::ASCENDING));

        $searchResult = $this->repository->search($criteria, $event->getSalesChannelContext()->getContext());

        $event->getPage()->addExtension('orderAttributes', $searchResult);
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

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function removeEmptyValues(array $payload): array
    {
        foreach ($payload as $key => $value) {
            if (is_array($value)) {
                $value = $this->removeEmptyValues($value);
            }

            if ($this->isEmptyValue($value)) {
                unset($payload[$key]);
                continue;
            }

            $payload[$key] = $value;
        }

        return $payload;
    }

    private function isEmptyValue(mixed $value): bool
    {
        return $value === null || $value === '' || (is_array($value) && empty($value));
    }
}
