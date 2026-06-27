<?php

declare(strict_types=1);

namespace Fyrst\OrderAttributes;

/**
 * Constants used throughout the OrderAttributes plugin.
 */
final class Constants
{
    /**
     * Payload key for order attributes stored in cart line items.
     * Data is moved from this payload key to customFields during order creation.
     */
    public const ORDER_ATTRIBUTES_KEY = 'orderAttributes';
}
