<?php

namespace Deco\Rates\Block;

use Deco\Rates\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

class UpdateTotalPrice extends Template
{
    protected $helperData;
    protected $collection;
    protected $configWriter;
    protected $setup;
    protected $productRepository;

    public function __construct(
        Template\Context $context,
        Data $helperData,
        CollectionFactory $collectionFactory,
        WriterInterface $configWriter,
        ModuleDataSetupInterface $setup,
        ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        $this->helperData = $helperData;
        $this->collection = $collectionFactory->create();
        $this->configWriter = $configWriter;
        $this->setup = $setup;
        $this->productRepository = $productRepository;
        parent::__construct($context, $data);
    }

    public function changeTotalPriceProductsValues($updateAll, $ratesToUpdate)
    {
        if($updateAll){
            foreach($this->collection as $product) {
                $product->save();
            }
        }else{
            foreach($this->collection as $product) {
                $productId = $product->getData('entity_id');
                $product = $this->productRepository->getById($productId);
                $taxaSelect = $this->helperData->getTaxaSelect($product->getAttributeText('taxa_select'));

                for($i = 0; $i < count($ratesToUpdate); $i++){
                    if($taxaSelect == $ratesToUpdate[$i]){
                        $product->save();
                    }
                }
            }
        }
    }
}