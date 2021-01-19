<?php

namespace Mazyl\Inpost\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();



        $connection = $installer->getConnection();
        $connection
            ->addColumn(
                $installer->getTable('quote_address'),
                'inpost_point',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table ::TYPE_TEXT,
                    'nullable' => true,
                    'default' => NULL,
                    'length' => 255,
                    'comment' => 'Inpost Point Name'
                ]
            );
        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'additional_address',
            [
                'length' => 30,
                'type' => 'text',
                'nullable' => false,
                'comment' => 'Additional address',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'additional_address',
            [
                'length' => 30,
                'type' => 'text',
                'nullable' => false,
                'comment' => 'Additional address',
            ]
        );

        $connection
            ->addColumn(
                $installer->getTable('sales_order_address'),
                'inpost_point',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table ::TYPE_TEXT,
                    'nullable' => true,
                    'default' => NULL,
                    'length' => 255,
                    'comment' => 'Inpost Point Name'
                ]
            );

        $connection
            ->addColumn(
                $installer->getTable('sales_order_address'),
                'inpost_package',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table ::TYPE_TEXT,
                    'nullable' => true,
                    'default' => NULL,
                    'length' => 255,
                    'comment' => 'Inpost Point Package Type'
                ]
            );
        $connection
            ->addColumn(
                $installer->getTable('sales_order_address'),
                'inpost_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table ::TYPE_TEXT,
                    'nullable' => true,
                    'default' => NULL,
                    'length' => 255,
                    'comment' => 'Inpost Point Package Type'
                ]
            );


        $installer->endSetup();

    }
}
