<?php

namespace Deco\Rates\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Db\Select;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface as UninstallInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class Uninstall
 */
class Uninstall implements UninstallInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $_eavSetupFactory;
    
    private $_mDSetup;
    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        ModuleDataSetupInterface $mDSetup
    )
    {
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->_mDSetup = $mDSetup;
    }

    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->_eavSetupFactory->create(['setup' => $this->_mDSetup]);
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'taxa_produto');
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'frete_fornecedor');
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'preco_total');
    }
}