<?php

declare(strict_types=1);

namespace Fyrst\OrderAttributes\Controller;

use Fyrst\OrderAttributes\Constants;
use Shopware\Core\Checkout\Cart\CartException;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
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
        $payload = $request->request->all('payload');

        if (!$lineItemId) {
            $this->addFlash(self::DANGER, $this->trans('error.message-default'));

            return $this->createActionResponse($request);
        }

        try {
            $cart = $this->cartService->getCart($context->getToken(), $context);
            $lineItem = $cart->getLineItems()->get($lineItemId);

            if (!$lineItem) {
                throw CartException::lineItemNotFound($lineItemId);
            }

            $lineItem->setPayloadValue(Constants::ORDER_ATTRIBUTES_KEY, $payload);

            $this->cartService->recalculate($cart, $context);

            return $this->renderOrderAttributesForm($lineItem);
        } catch (\Exception $e) {
            $this->addFlash(self::DANGER, $this->trans('error.message-default'));

            return $this->createActionResponse($request);
        }
    }

    private function renderOrderAttributesForm(LineItem $lineItem): Response
    {
        return $this->renderStorefront(
            '@Storefront/storefront/component/line-item/order-attributes-form.html.twig',
            ['lineItem' => $lineItem]
        );
    }
}
