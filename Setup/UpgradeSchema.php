<?php

namespace Deco\Rates\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface {

    /**
     * {@inheritdoc}
     */
    public function upgrade(
    SchemaSetupInterface $setup, ModuleContextInterface $context
    ) {
        shell_exec('updateTotalPrice.php');
    }
}