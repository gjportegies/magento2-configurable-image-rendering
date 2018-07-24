<?php

namespace SR\ConfigurableImageRendering\Plugin;


use SR\ConfigurableImageRendering\Helper\Data;
use Magento\Catalog\Block\Product\View\Gallery;

class GalleryPlugin
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
     * @param Gallery $subject
     * @param callable $proceed
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundGetGalleryImages(Gallery $subject, callable $proceed)
    {
        $productWithLowestPrice = $this->helper->getProductWithLowestPrice($subject->getProduct());
        if ($productWithLowestPrice->getId() != $subject->getProduct()->getId()) {
            $subject->setProduct($productWithLowestPrice);
        }

        $result = $proceed();

        return $result;
    }
}