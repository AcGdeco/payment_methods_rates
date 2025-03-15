<?php

namespace Deco\Rates\Block;

use Deco\Rates\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Config\Storage\WriterInterface;

class UpdateTotalPrice extends Template
{
    protected $helperData;
    protected $collection;
    protected $configWriter;

    public function __construct(
        Template\Context $context,
        Data $helperData,
        CollectionFactory $collectionFactory,
        WriterInterface $configWriter,
        array $data = []
    ) {
        $this->helperData = $helperData;
        $this->collection = $collectionFactory->create();
        $this->configWriter = $configWriter;
        parent::__construct($context, $data);
    }

    public function addTotalPriceProductsValues()
    {
        foreach($this->collection as $product) {
            $product = $product->save();
        }
    }

    public function changeValueRunScript()
    {
        $this->configWriter->save(
            'decorates/general/run_script',
            0,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            0
        );
    }
}
