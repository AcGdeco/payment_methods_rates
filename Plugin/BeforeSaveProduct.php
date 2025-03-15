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
        if($this->helperData->getEnable()) {
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

        return [$product, $saveOptions];
    }
}