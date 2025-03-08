<?php
 
namespace Deco\Rates\Plugin;

use Deco\Rates\Helper\Data as HelperData;

class Price
{
    protected $helperData;
    
    public function __construct(
        HelperData $helperData
    ) {
        $this->helperData = $helperData;
    }

    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        if($this->helperData->getEnable() && !$this->helperData->getIfAPICall()) {
            $taxaProduto = $subject->getData('taxa_produto') ? $subject->getData('taxa_produto') : null;
            $freteFornecedor = $subject->getData('frete_fornecedor') ? $subject->getData('frete_fornecedor') : null;
            $result = $this->helperData->getTotalPercentageRatePrice($result, $taxaProduto, $freteFornecedor);
        }

        return $result;
    }
}