<?php
 
namespace Deco\Rates\Plugin;

use Deco\Rates\Helper\Data as HelperData;

class Price
{
    protected $helperData;
    
    public function __construct(
        HelperData $helperData,
    ) {
        $this->helperData = $helperData;
    }

    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        if($this->helperData->getEnable()) {
            $taxaProduto = $subject->getData('taxa_produto') ? $subject->getData('taxa_produto') : null;
            $result = $this->helperData->getTotalPercentageRatePrice($result, $taxaProduto);
        }

        return $result;
    }
}