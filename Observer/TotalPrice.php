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
            $_product = $observer->getProduct();
            $id = $_product->getData("entity_id");
            $price = $_product->getPrice();
            if($price == null){
                $product = $this->product->load($id);
                $price = $product->getPrice();
            }
            $_product->setPrecoTotal($price);
        }
    }
}