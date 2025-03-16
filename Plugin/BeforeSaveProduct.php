<?php
namespace Deco\Rates\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Deco\Rates\Helper\Data as HelperData;

class BeforeSaveProduct
{
    protected $helperData;
    
    public function __construct(
        HelperData $helperData
    ) {
        $this->helperData = $helperData;
    }

    public function beforeSave(ProductRepositoryInterface $subject, ProductInterface $product, $saveOptions = false)
    {
        if($this->helperData->getEnable() && !$this->helperData->getIfRestV1ProductsAPI()) {
            $this->helperData->setProductPrice($product);
        }

        return [$product, $saveOptions];
    }
}