<?php
namespace Webkul\Test\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Deco\Rates\Block\UpdateTotalPrice;

class UpgradeData implements UpgradeDataInterface
{
    protected $updateTotalPrice;

    public function __construct(
        UpdateTotalPrice $updateTotalPrice
    ) {
        $this->updateTotalPrice = $updateTotalPrice;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->updateTotalPrice->addTotalPriceProductsValues();
    }
}