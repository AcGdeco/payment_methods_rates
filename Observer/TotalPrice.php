<?php
namespace Deco\Rates\Observer;

use Magento\Framework\Event\ObserverInterface;
use Deco\Rates\Helper\Data as HelperData;
use Magento\Catalog\Model\Product;

class TotalPrice implements ObserverInterface
{
    protected $helperData;
    protected $product;
    
    public function __construct(
        HelperData $helperData,
        Product $product
    ) {
        $this->helperData = $helperData;
        $this->product = $product;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {

        if($this->helperData->getEnable()) {
            $taxaProduto = $this->product->getData('taxa_produto') || $this->product->getData('taxa_produto') == 0 ? $this->product->getData('taxa_produto') : null;
            $freteFornecedor = $this->product->getData('frete_fornecedor') ? $this->product->getData('frete_fornecedor') : null;
            $precoFornecedor = $this->product->getData('preco_fornecedor') ? $this->product->getData('preco_fornecedor') : null;
            $preco = $this->helperData->getTotalPercentageRatePrice($precoFornecedor, $taxaProduto, $freteFornecedor);
            $this->product->setPrice($preco);
        }
    }
}