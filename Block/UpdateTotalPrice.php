<?php

namespace Deco\Rates\Block;

use Deco\Rates\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class UpdateTotalPrice extends Template
{
    protected $helperData;
    protected $collection;

    public function __construct(
        Template\Context $context,
        Data $helperData,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->helperData = $helperData;
        $this->collection = $collectionFactory->create();
        parent::__construct($context, $data);
    }

    public function addTotalPriceProductsValues()
    {
        foreach($this->collection as $product) {
            $product = $product->save();
        }
    }
}
