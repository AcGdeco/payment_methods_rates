<?php

namespace Deco\Rates\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $scopeConfig;
    protected $storeManager;
    protected $urlInterface;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        UrlInterface $urlInterface
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->urlInterface = $urlInterface;
    }

    public function setProductPrice()
    {
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

    public function getIfRestV1ProductsAPI()
    {
        $urlBase = $this->storeManager->getStore()->getBaseUrl();
        $urlCall = $this->urlInterface->getCurrentUrl();
        $substring = $urlBase."rest/V1/products";

        if (strpos($urlCall, $substring) !== false) {
            $isRestV1ProductsAPI = true;
        } else {
            $isRestV1ProductsAPI = false;
        }

        return $isRestV1ProductsAPI;
    }

    public function getEnable()
    {
        return $this->scopeConfig->getValue(
            'decorates/general/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getInstallmentsFeesEnable()
    {
        return $this->scopeConfig->getValue(
            'decorates/installments_fees/installments_fees_enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getPercentageRate()
    {
        return $this->scopeConfig->getValue(
            'decorates/general/rate_percentage',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getProductPercentageRate()
    {
        $productRatePercentage = [
            $this->scopeConfig->getValue(
                'decorates/general/product_rate_percentage_1',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ),
            $this->scopeConfig->getValue(
                'decorates/general/product_rate_percentage_2',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ),
            $this->scopeConfig->getValue(
                'decorates/general/product_rate_percentage_3',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ),
            $this->scopeConfig->getValue(
                'decorates/general/product_rate_percentage_4',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        ];
        
        return $productRatePercentage;
    }

    public function getUnalterableProductPercentageRate()
    {
        return $this->scopeConfig->getValue(
            'decorates/general/unalterable_product_rate_percentage',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getNumericalRate()
    {
        return $this->scopeConfig->getValue(
            'decorates/general/rate_value',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getInstallmentsFees()
    {
        for($i = 0; $i <= 17; $i++){
            $installementsFees[$i] = $this->scopeConfig->getValue(
                'decorates/installments_fees/fee_'.$i,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        
        return $installementsFees;
    }

    public function getTotalPercentageRatePrice($price, $taxaProduto, $freteFornecedor)
    {
        
        $ratePrice = $price;
        if($price != 0){
            if($freteFornecedor != null){
                $ratePrice = $ratePrice + $freteFornecedor;
            }
            if($taxaProduto != null) {
                $ratePrice = $ratePrice + $ratePrice * $taxaProduto / 100;
            } else if(!empty($this->getProductPercentageRate())){
                $productPercentageRate = $this->getProductPercentageRate();
                $ratePrice = $ratePrice + $ratePrice * $productPercentageRate / 100;
            }
            if(!empty($this->getUnalterableProductPercentageRate())) {
                $unalterableProductPercentageRate = $this->getUnalterableProductPercentageRate();
                $rateUnalterablePrice = $price * $unalterableProductPercentageRate / 100;
                $ratePrice = $ratePrice + $rateUnalterablePrice;
            }
            if(!empty($this->getPercentageRate())) {
                $percentageRate = $this->getPercentageRate();
                $ratePrice = $ratePrice / (1 - ($percentageRate / 100));
            }
        }
        return $ratePrice;
    }

    public function getNumericalPercentageRatePrice($price)
    {
        $ratePrice = $price;
        if($price != 0){
            if(!empty($this->getPercentageRate()) && !empty($this->getNumericalRate())){
                $percentageRate = $this->getPercentageRate();
                $numericalRate = $this->getNumericalRate();

                $ratePrice = ($price + $numericalRate) / (1 - ($percentageRate / 100));
            }else if(!empty($this->getPercentageRate())){
                $percentageRate = $this->getPercentageRate();

                $ratePrice = ($price) / (1 - ($percentageRate / 100));
            } else if(!empty($this->getNumericalRate())){
                $numericalRate = $this->getNumericalRate();

                $ratePrice = $price + $numericalRate;
            } 
        }
        return $ratePrice;
    }
}