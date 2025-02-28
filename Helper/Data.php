<?php

namespace Deco\Rates\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getEnable()
    {
        return $this->scopeConfig->getValue(
            'decorates/general/enable',
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
        return $this->scopeConfig->getValue(
            'decorates/general/product_rate_percentage',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
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

    public function getTotalPercentageRatePrice($price, $taxaProduto)
    {
        
        $ratePrice = $price;
        if($price != 0){
            if($taxaProduto != null){
                $ratePrice = $ratePrice + $ratePrice * $taxaProduto / 100;
            }else if(!empty($this->getProductPercentageRate())){
                $productPercentageRate = $this->getProductPercentageRate();
                $ratePrice = $ratePrice + $ratePrice * $productPercentageRate / 100;
            }
            if(!empty($this->getUnalterableProductPercentageRate())){
                $unalterableProductPercentageRate = $this->getUnalterableProductPercentageRate();
                $rateUnalterablePrice = $price * $unalterableProductPercentageRate / 100;
                $ratePrice = $ratePrice + $rateUnalterablePrice;
            }
            if(!empty($this->getPercentageRate())){
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