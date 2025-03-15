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