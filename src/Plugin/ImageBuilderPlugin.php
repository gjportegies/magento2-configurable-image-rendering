<?php

namespace SR\ConfigurableImageRendering\Plugin;


use SR\ConfigurableImageRendering\Helper\Data;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Block\Product\ImageBuilder;

class ImageBuilderPlugin
{

    /**
     * @var Data
     */
    private $helper;

    /**
     * ImageBuilderPlugin constructor.
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param ImageBuilder $subject
     * @param Product $product
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeSetProduct(ImageBuilder $subject, Product $product)
    {
        $productWithLowestPrice = $this->helper->getProductWithLowestPrice($product);
        if ($productWithLowestPrice->getId() != $product->getId()) {
            $product = $productWithLowestPrice;
        }
        return [$product];
    }

}