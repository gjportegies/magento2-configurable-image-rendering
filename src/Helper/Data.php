<?php

namespace SR\ConfigurableImageRendering\Helper;


use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var StockItemRepository
     */
    protected $stockItemRepository;

    /**
     * Data constructor.
     * @param Context $context
     * @param ProductRepository $productRepository
     * @param StockItemRepository $stockItemRepository
     */
    public function __construct(
        Context $context,
        ProductRepository $productRepository,
        StockItemRepository $stockItemRepository
    ) {
        $this->productRepository = $productRepository;
        $this->stockItemRepository = $stockItemRepository;
        parent::__construct($context);
    }


    /**
     * @param $product
     * @return \Magento\Catalog\Api\Data\ProductInterface|mixed
     * @throws NoSuchEntityException
     */
    public function getProductWithLowestPrice($product)
    {
        $productPrices = [];
        if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $childProducts = $product->getTypeInstance()->getUsedProducts($product);

            foreach ($childProducts as $child) {
                $productPrices[$child->getId()]['price'] = $child->getFinalPrice();
                //TODO: fetch all products qty by ids
                $productPrices[$child->getId()]['qty'] = $this->stockItemRepository->get($child->getId())->getQty();
            }
            $productId = $this->getProductId($productPrices);
            try {
                return $this->productRepository->getById($productId);
            } catch (NoSuchEntityException $e) {
              return $product;
            }
        } else {
            return $product;
        }
    }

    /**
     * Find a product Id from array by lowest price and greatest quantity
     *
     * @param $products
     * @return mixed
     */
    protected function getProductId($products)
    {
        $sort = array();
        $temp = $products;
        foreach($products as $key => $value) {
            $sort['price'][$key] = $value['price'];
            $sort['qty'][$key] = $value['qty'];
        }

        //Sort arrays by 2 values
        array_multisort($sort['price'], SORT_ASC, $sort['qty'], SORT_DESC, $products);

        $productId;
        foreach ($temp as $key => $value) {
            if($value['qty'] == $products[0]['qty'] && $value['price'] == $products[0]['price']) {
                $productId = $key;
                break;
            }
        }

        return $productId;
    }
}