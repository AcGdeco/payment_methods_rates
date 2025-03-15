<?php
namespace Deco\Rates\Observer;

use Magento\Framework\Event\ObserverInterface;
use Deco\Rates\Helper\Data as HelperData;

class TotalPrice implements ObserverInterface
{
    protected $helperData;
    
    public function __construct(
        HelperData $helperData
    ) {
        $this->helperData = $helperData;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        if($this->helperData->getEnable()) {
            $product = $observer->getData('product');
            $taxaProduto = $product->getData('taxa_produto') || $product->getData('taxa_produto') == 0 ? $product->getData('taxa_produto') : null;
            $freteFornecedor = $product->getData('frete_fornecedor') ? $product->getData('frete_fornecedor') : null;
            $precoFornecedor = $product->getData('preco_fornecedor') ? $product->getData('preco_fornecedor') : null;
            $preco = $this->helperData->getTotalPercentageRatePrice($precoFornecedor, $taxaProduto, $freteFornecedor);
            $product->setPrice($preco);

            if($product->getPrecoEspecial() != null && $product->getPrecoEspecial() != ""){
                $specialPrice = $this->helperData->getTotalPercentageRatePrice($product->getPrecoEspecial(), $taxaProduto, $freteFornecedor);
                $product->setSpecialPrice($specialPrice);
            }
        }
    }
}