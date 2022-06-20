<?php

namespace Genesisoft\ContinueShopping\Block;

use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\Session;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Quote\Api\Data\CartItemInterface;

class ContinueShopping extends Template
{
    /**
     * @var Session
     */
    private $checkoutSession;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var Configurable
     */
    private $configurableType;

    public function __construct(
        Template\Context $context,
        array $data = array(),
        Session $checkoutSession,
        ProductRepository $productRepository,
        Configurable $configurableType)
    {
        parent::__construct($context, $data);
        $this->checkoutSession = $checkoutSession;
        $this->productRepository = $productRepository;
        $this->configurableType = $configurableType;
    }

    protected function _prepareLayout()
    {
    }

    public function getUrlContinueShopping()
    {
        $items = $this->checkoutSession->getQuote()->getItems();
        /** @var CartItemInterface $item */
        $item = end($items);

        try{
            $_product = $this->productRepository->get($item->getSku());
            $parent = $this->configurableType->getParentIdsByChild($_product->getId());
            if(end($parent)){
                $_productParent = $this->productRepository->getById(end($parent));
                return $_productParent->getProductUrl();
            }

            return $_product->getProductUrl();
        }catch (NoSuchEntityException $exception){
            return '/loja-online.html';
        }
    }
}
