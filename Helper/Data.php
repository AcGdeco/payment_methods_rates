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
        $productPercentageRate = $this->getProductPercentageRate();
        $percentageRate = $this->getPercentageRate();
        $generalPercentageRate = $this->getGeneralPercentageRate();
        $isDifferent = false;
        $ratesToUpdate = [];
        $updateAll = false;

        for($i = 0; $i < count($productPercentageRate); $i++){
            if($productPercentageRate[$i] != $groups['general']['fields']['product_rate_percentage_'.$i+1]['value']){
                $isDifferent = true;
                $ratesToUpdate[] = $i + 1;
            }
        }

        for($i = 0; $i < count($generalPercentageRate); $i++){
            if($generalPercentageRate[$i] != $groups['general']['fields']['general_rate_percentage_'.$i+1]['value']){
                $isDifferent = true;
                $updateAll = true;
            }
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
            $updateAll = true;
        }

        return [$isDifferent, $percentageRateOld, $updateAll, $ratesToUpdate];
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
        } else {
            $product->setSpecialPrice();
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

    public function getGeneralPercentageRate()
    {
        $productRatePercentage = [
            $this->scopeConfig->getValue(
                'decorates/general/general_rate_percentage_1',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ),
            $this->scopeConfig->getValue(
                'decorates/general/general_rate_percentage_2',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        ];
        
        return $productRatePercentage;
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

    public function getShippingApplyRatePercentage(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            'decorates/shipping_rates/apply_rate_percentage',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getShippingApplyRateValue(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            'decorates/shipping_rates/apply_rate_value',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getShippingApplyGeneralRate1(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            'decorates/shipping_rates/apply_general_rate_1',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getShippingApplyGeneralRate2(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            'decorates/shipping_rates/apply_general_rate_2',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Calculate the final shipping price applying only the rates that are
     * individually enabled in Stores > Configuration > Sales > Rates >
     * Taxas Aplicadas ao Frete.
     *
     * Calculation order:
     *  1. General Rate 1 (direct %)
     *  2. General Rate 2 (direct %)
     *  3. Numerical Rate (fixed addition)
     *  4. Percentage Rate (inverse: price / (1 - rate/100))  ← applied last
     *
     * @param float $price Original shipping price in store currency.
     * @return float
     */
    public function getShippingRatePrice(float $price): float
    {
        if ($price == 0) {
            return $price;
        }

        $basePrice  = $price;
        $ratePrice  = $price;
        $generalRates = $this->getGeneralPercentageRate();

        // 1. General Percentage Rate 1 (direct addition based on original price)
        if ($this->getShippingApplyGeneralRate1() && !empty($generalRates[0])) {
            $ratePrice += $basePrice * ((float) $generalRates[0]) / 100;
        }

        // 2. General Percentage Rate 2 (direct addition based on original price)
        if ($this->getShippingApplyGeneralRate2() && !empty($generalRates[1])) {
            $ratePrice += $basePrice * ((float) $generalRates[1]) / 100;
        }

        // 3. Numerical Rate (fixed value)
        if ($this->getShippingApplyRateValue()) {
            $numericalRate = (float) $this->getNumericalRate();
            if ($numericalRate != 0) {
                $ratePrice += $numericalRate;
            }
        }

        // 4. Percentage Rate (inverse calculation — covers payment fees passed through)
        if ($this->getShippingApplyRatePercentage()) {
            $percentageRate = (float) $this->getPercentageRate();
            if ($percentageRate != 0) {
                $ratePrice = $ratePrice / (1 - ($percentageRate / 100));
            }
        }

        return $ratePrice;
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

            $generalPercentageRate = $this->getGeneralPercentageRate();
            for($i = 0; $i < count($generalPercentageRate); $i++){
                if($generalPercentageRate[$i] != null) {
                    $ratePrice = $ratePrice + $totalPrice * $generalPercentageRate[$i] / 100;
                }
            }

            if(!empty($taxaSelect)){
                $productPercentageRate = $this->getProductPercentageRate();

                if(!empty($productPercentageRate[$taxaSelect-1])){
                    $ratePrice = $ratePrice + $totalPrice * $productPercentageRate[$taxaSelect - 1] / 100;
                }
            }

            if(!empty($this->getPercentageRate())) {
                $percentageRate = $this->getPercentageRate();
                if($percentageRate != 0){
                    $ratePrice = $ratePrice / (1 - ($percentageRate / 100));
                }
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