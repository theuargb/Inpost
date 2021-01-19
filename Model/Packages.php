<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mazyl\Inpost\Model;

use Magento\Framework\Api\DataObjectHelper;
use Mazyl\Inpost\Api\Data\PackagesInterface;
use Mazyl\Inpost\Api\Data\PackagesInterfaceFactory;

class Packages extends \Magento\Framework\Model\AbstractModel
{

    protected $packagesDataFactory;

    protected $dataObjectHelper;

    protected $_eventPrefix = 'mazyl_inpost_packages';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param PackagesInterfaceFactory $packagesDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Mazyl\Inpost\Model\ResourceModel\Packages $resource
     * @param \Mazyl\Inpost\Model\ResourceModel\Packages\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        PackagesInterfaceFactory $packagesDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Mazyl\Inpost\Model\ResourceModel\Packages $resource,
        \Mazyl\Inpost\Model\ResourceModel\Packages\Collection $resourceCollection,
        array $data = []
    ) {
        $this->packagesDataFactory = $packagesDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve packages model with packages data
     * @return PackagesInterface
     */
    public function getDataModel()
    {
        $packagesData = $this->getData();
        
        $packagesDataObject = $this->packagesDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $packagesDataObject,
            $packagesData,
            PackagesInterface::class
        );
        
        return $packagesDataObject;
    }
}

