<?php

namespace Deco\Rates\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Deco\Rates\Block\UpdateTotalPrice;

class ProductsAllTotalPrice implements ObserverInterface
{
    /**
     * @var UpdateTotalPrice
     */
    protected $updateTotalPrice;

    /**
     * @param UpdateTotalPrice $updateTotalPrice
     */
    public function __construct(
        UpdateTotalPrice $updateTotalPrice
    ) {
        $this->updateTotalPrice = $updateTotalPrice;
    }

    public function execute(EventObserver $observer)
    {
        $this->updateTotalPrice->addTotalPriceProductsValues();
    }
}
