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
            $this->helperData->setProductPrice();
        }
    }
}