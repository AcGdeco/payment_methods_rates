<?php
namespace Deco\Rates\Observer;

use Magento\Framework\Event\ObserverInterface;
use Deco\Rates\Helper\Data as HelperData;
use Magento\Catalog\Api\ProductRepositoryInterface;

class TotalPrice implements ObserverInterface
{
    protected $helperData;
    protected $productRepository;
    
    public function __construct(
        HelperData $helperData,
        ProductRepositoryInterface $productRepository
    ) {
        $this->helperData = $helperData;
        $this->productRepository = $productRepository;
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        if($this->helperData->getEnable()) {
            $path = 'admin/admin/system_config';
            if($this->helperData->getCompareURL($path)) {
                $productId = $observer->getData('product')->getData('entity_id');
                $product = $this->productRepository->getById($productId);
                $this->helperData->setProductPrice($product);
                $observer->getData('product')->setPrice($product->getPrice());
            } else {
                $this->helperData->setProductPrice($observer->getData('product'));
            }
            
        }
    }
}