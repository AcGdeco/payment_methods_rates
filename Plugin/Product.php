<?php
 
namespace Deco\Rates\Plugin;

use Deco\Rates\Helper\Data as HelperData;

class Product
{
    protected $helperData;
    
    public function __construct(
        HelperData $helperData
    ) {
        $this->helperData = $helperData;
    }

    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        if($this->helperData->getEnable()) {
            $result = $this->helperData->getPercentageRatePrice($result);
        }

        return $result;
    }
}