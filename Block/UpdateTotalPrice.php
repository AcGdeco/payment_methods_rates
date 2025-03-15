<?php

namespace Deco\Rates\Block;

use Deco\Rates\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpdateTotalPrice extends Template
{
    protected $helperData;
    protected $collection;
    protected $configWriter;
    protected $setup;

    public function __construct(
        Template\Context $context,
        Data $helperData,
        CollectionFactory $collectionFactory,
        WriterInterface $configWriter,
        ModuleDataSetupInterface $setup,
        array $data = []
    ) {
        $this->helperData = $helperData;
        $this->collection = $collectionFactory->create();
        $this->configWriter = $configWriter;
        $this->setup = $setup;
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
        $this->setup->startSetup();

        $this->configWriter->save(
            'decorates/general/run_script',
            0,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            0
        );

        $this->setup->endSetup();
    }
}
