<?php

namespace Deco\Rates\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Deco\Rates\Block\UpdateTotalPrice;
use Deco\Rates\Helper\Data;

class ProductsAllTotalPrice implements ObserverInterface
{
    /**
     * @var UpdateTotalPrice
     */
    protected $updateTotalPrice;
    
    /**
     * @var UpdateTotalPrice
     */
    protected $helperData;

    /**
     * @param UpdateTotalPrice $updateTotalPrice
     */
    public function __construct(
        UpdateTotalPrice $updateTotalPrice,
        Data $helperData
    ) {
        $this->updateTotalPrice = $updateTotalPrice;
        $this->helperData = $helperData;
    }

    public function execute(EventObserver $observer)
    {
        if($this->helperData->getRunScript()){
            $this->updateTotalPrice->addTotalPriceProductsValues();
            $this->updateTotalPrice->changeValueRunScript();
        }
    }
}
