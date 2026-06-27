<?php

declare(strict_types=1);

namespace Fyrst\OrderAttributes\Controller;

use Fyrst\OrderAttributes\Constants;
use Shopware\Core\Checkout\Cart\CartException;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
#[Package('checkout')]
class OrderAttributesController extends StorefrontController
{
    public function __construct(
        private readonly CartService $cartService
    ) {}

    #[Route(
        path: '/order-attributes/add',
        name: 'frontend.orderAttributes.add',
        methods: ['POST'],
        defaults: ['XmlHttpRequest' => true, 'csrf_protected' => false]
    )]
    public function add(Request $request, SalesChannelContext $context): Response
    {
        $lineItemId = $request->request->get('lineItemId');
        $payload = $request->request->all(Constants::ORDER_ATTRIBUTES_KEY);

        if (!$lineItemId) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Missing lineItemId',
                'data' => [],
            ], 400);
        }

        try {
            $cart = $this->cartService->getCart($context->getToken(), $context);
            $lineItem = $cart->getLineItems()->get($lineItemId);

            if (!$lineItem) {
                throw CartException::lineItemNotFound($lineItemId);
            }

            $lineItem->setPayloadValue(Constants::ORDER_ATTRIBUTES_KEY, $payload);

            $this->cartService->recalculate($cart, $context);

            return new JsonResponse([
                'success' => true,
                'data' => $payload,
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
