<?php

namespace Deco\Rates\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Deco\Rates\Block\UpdateTotalPrice;

class UpgradeSchema implements UpgradeSchemaInterface {

    public function __construct(
        UpdateTotalPrice $updateTotalPrice
    ) {
        $this->updateTotalPrice = $updateTotalPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(
    SchemaSetupInterface $setup, ModuleContextInterface $context
    ) {
        $this->updateTotalPrice->addTotalPriceProductsValues();
    }
}