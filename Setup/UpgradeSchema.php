<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    Codilar_Gst Extension
 * @package
 * @copyright   Copyright (c) 2017 Codilar. (http://www.codilar.com)
 * @purpose
 * @author       Codilar Team
 **/
namespace Codilar\Gst\Setup;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Model\Config;
use Magento\Framework\DB\Ddl\Table;
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Attribute Code of the Custom Attribute
     */
    const CUSTOM_ATTRIBUTE_CODE = 'gstin';

    /**
     * @var EavSetup
     */
    private $eavSetup;

    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * InstallData constructor.
     * @param EavSetup $eavSetup
     * @param Config $config
     */
    public function __construct(
        EavSetup $eavSetup,
        Config $config
    ) {
        $this->eavSetup = $eavSetup;
        $this->eavConfig = $config;
    }
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_tax'),
                'sgst',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'comment' => 'SGST',
                    'default' => '0'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_tax'),
                'cgst',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'comment' => 'CGST',
                    'default' => '0'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_tax'),
                'igst',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'comment' => 'IGST',
                    'default' => '0'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_aggregated_created'),
                'sgst',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'comment' => 'SGST',
                    'default' => '0'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_aggregated_created'),
                'cgst',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'comment' => 'CGST',
                    'default' => '0'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_aggregated_created'),
                'igst',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'comment' => 'IGST',
                    'default' => '0'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_aggregated_updated'),
                'sgst',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'comment' => 'SGST',
                    'default' => '0'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_aggregated_updated'),
                'cgst',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'comment' => 'CGST',
                    'default' => '0'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_aggregated_updated'),
                'igst',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'comment' => 'IGST',
                    'default' => '0'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('tax_order_aggregated_created'),
                'sgst',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'comment' => 'SGST',
                    'default' => '0'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('tax_order_aggregated_created'),
                'cgst',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'comment' => 'CGST',
                    'default' => '0'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('tax_order_aggregated_created'),
                'igst',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'comment' => 'IGST',
                    'default' => '0'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('tax_order_aggregated_updated'),
                'sgst',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'comment' => 'SGST',
                    'default' => '0'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('tax_order_aggregated_updated'),
                'cgst',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'comment' => 'CGST',
                    'default' => '0'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('tax_order_aggregated_updated'),
                'igst',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'length' => '12,4',
                    'comment' => 'IGST',
                    'default' => '0'
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order_address'),
                'gstin',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'GSTIN',
                    'default' => ''
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('quote_address'),
                'gstin',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'length' => '255',
                    'comment' => 'GSTIN',
                    'default' => ''
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $this->eavSetup->addAttribute(
                AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
                self::CUSTOM_ATTRIBUTE_CODE,
                [
                    'label' => 'GSTIN',
                    'input' => 'text',
                    'visible' => true,
                    'required' => false,
                    'position' => 150,
                    'sort_order' => 150,
                    'system' => false
                ]
            );

            $customAttribute = $this->eavConfig->getAttribute(
                AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
                self::CUSTOM_ATTRIBUTE_CODE
            );

            $customAttribute->setData(
                'used_in_forms',
                ['adminhtml_customer_address', 'customer_address_edit', 'customer_register_address']
            );

            $customAttribute->save();
        }


    }
}