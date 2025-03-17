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

    public function getCompareValuesConfig($groups)
    {
        $productRatePercentage = $this->getProductPercentageRate();
        $percentageRate = $this->getPercentageRate();

        $isDifferent = false;
        if(
            $productRatePercentage[0] != $groups['general']['fields']['product_rate_percentage_1']['value'] ||
            $productRatePercentage[1] != $groups['general']['fields']['product_rate_percentage_2']['value'] ||
            $productRatePercentage[2] != $groups['general']['fields']['product_rate_percentage_3']['value'] ||
            $productRatePercentage[3] != $groups['general']['fields']['product_rate_percentage_4']['value'] ||
            $productRatePercentage[4] != $groups['general']['fields']['product_rate_percentage_5']['value']
        ){
            $isDifferent = true;
        }
        
        $percentageRateOld = false;
        if(!array_key_exists('value', $groups['general']['fields']['rate_percentage'])) {
            $percentageRateOld = $percentageRate;
        }

        if(
            array_key_exists('value', $groups['general']['fields']['rate_percentage']) &&
            $percentageRate != $groups['general']['fields']['rate_percentage']['value']
        ){
            $isDifferent = true;
        }

        return [$isDifferent, $percentageRateOld];
    }

    public function getTaxaSelect($taxaSelect)
    {
        if($taxaSelect == 'Taxa 1'){
            $taxaSelect = 1;
        }
        if($taxaSelect == 'Taxa 2'){
            $taxaSelect = 2;
        }
        if($taxaSelect == 'Taxa 3'){
            $taxaSelect = 3;
        }
        if($taxaSelect == 'Taxa 4'){
            $taxaSelect = 4;
        }
        if($taxaSelect == 'Taxa 5'){
            $taxaSelect = 5;
        }

        return $taxaSelect;
    }

    public function setProductPrice($product)
    {
        $taxaProduto = $product->getData('taxa_produto') || $product->getData('taxa_produto') == 0 ? $product->getData('taxa_produto') : null;
        $taxaSelect = $this->getTaxaSelect($product->getAttributeText('taxa_select'));
        $freteFornecedor = $product->getData('frete_fornecedor') ? $product->getData('frete_fornecedor') : null;
        $precoFornecedor = $product->getData('preco_fornecedor') ? $product->getData('preco_fornecedor') : null;
        $preco = $this->getTotalPercentageRatePrice($precoFornecedor, $taxaProduto, $freteFornecedor, $taxaSelect);
        $product->setPrice($preco);

        if($product->getPrecoEspecial() != null && $product->getPrecoEspecial() != ""){
            $specialPrice = $this->getTotalPercentageRatePrice($product->getPrecoEspecial(), $taxaProduto, $freteFornecedor, $taxaSelect);
            $product->setSpecialPrice($specialPrice);
        }
    }

    public function getCompareURL($path) {
        $urlBase = $this->storeManager->getStore()->getBaseUrl();
        $urlCall = $this->urlInterface->getCurrentUrl();
        $substring = $urlBase.$path;

        if (strpos($urlCall, $substring) !== false) {
            $result = true;
        } else {
            $result = false;
        }

        return $result;
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
            ),
            $this->scopeConfig->getValue(
                'decorates/general/product_rate_percentage_5',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        ];
        
        return $productRatePercentage;
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

    public function getTotalPercentageRatePrice($price, $taxaProduto, $freteFornecedor, $taxaSelect)
    {
        $ratePrice = 0;
        if($price != 0){
            if($freteFornecedor != null){
                $totalPrice = $price + $freteFornecedor;
                $ratePrice = $totalPrice;
            } else {
                $totalPrice = $price;
                $ratePrice = $totalPrice;
            }
            if($taxaProduto != null) {
                $ratePrice = $ratePrice + $ratePrice * $taxaProduto / 100;
            }
            if(!empty($taxaSelect)){
                $productPercentageRate = $this->getProductPercentageRate();

                if(!empty($productPercentageRate[$taxaSelect-1])){
                    $ratePrice = $ratePrice + $totalPrice * $productPercentageRate[$taxaSelect - 1] / 100;
                }
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