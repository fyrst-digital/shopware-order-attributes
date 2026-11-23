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

            if (empty($orderAttributes)) {
                continue;
            }

            $orderAttributes = $this->sanitizePayload($orderAttributes);

            $customFields = $convertedCart['lineItems'][$index]['customFields'] ?? [];
            $convertedCart['lineItems'][$index]['customFields'] = $this->setOrderAttributes($orderAttributes, $customFields);

            unset($convertedCart['lineItems'][$index]['payload'][Constants::ORDER_ATTRIBUTES_KEY]);
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
