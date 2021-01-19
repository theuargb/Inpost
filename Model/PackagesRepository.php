<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mazyl\Inpost\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Mazyl\Inpost\Api\Data\PackagesInterfaceFactory;
use Mazyl\Inpost\Api\Data\PackagesSearchResultsInterfaceFactory;
use Mazyl\Inpost\Api\PackagesRepositoryInterface;
use Mazyl\Inpost\Model\ResourceModel\Packages as ResourcePackages;
use Mazyl\Inpost\Model\ResourceModel\Packages\CollectionFactory as PackagesCollectionFactory;

class PackagesRepository implements PackagesRepositoryInterface
{

    protected $resource;

    protected $packagesFactory;

    protected $packagesCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataPackagesFactory;

    protected $extensionAttributesJoinProcessor;

    private $storeManager;

    private $collectionProcessor;

    protected $extensibleDataObjectConverter;

    /**
     * @param ResourcePackages $resource
     * @param PackagesFactory $packagesFactory
     * @param PackagesInterfaceFactory $dataPackagesFactory
     * @param PackagesCollectionFactory $packagesCollectionFactory
     * @param PackagesSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourcePackages $resource,
        PackagesFactory $packagesFactory,
        PackagesInterfaceFactory $dataPackagesFactory,
        PackagesCollectionFactory $packagesCollectionFactory,
        PackagesSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->packagesFactory = $packagesFactory;
        $this->packagesCollectionFactory = $packagesCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPackagesFactory = $dataPackagesFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Mazyl\Inpost\Api\Data\PackagesInterface $packages
    ) {
        /* if (empty($packages->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $packages->setStoreId($storeId);
        } */
        
        $packagesData = $this->extensibleDataObjectConverter->toNestedArray(
            $packages,
            [],
            \Mazyl\Inpost\Api\Data\PackagesInterface::class
        );
        
        $packagesModel = $this->packagesFactory->create()->setData($packagesData);
        
        try {
            $this->resource->save($packagesModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the packages: %1',
                $exception->getMessage()
            ));
        }
        return $packagesModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($packagesId)
    {
        $packages = $this->packagesFactory->create();
        $this->resource->load($packages, $packagesId);
        if (!$packages->getId()) {
            throw new NoSuchEntityException(__('Packages with id "%1" does not exist.', $packagesId));
        }
        return $packages->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->packagesCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Mazyl\Inpost\Api\Data\PackagesInterface::class
        );
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Mazyl\Inpost\Api\Data\PackagesInterface $packages
    ) {
        try {
            $packagesModel = $this->packagesFactory->create();
            $this->resource->load($packagesModel, $packages->getPackagesId());
            $this->resource->delete($packagesModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Packages: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($packagesId)
    {
        return $this->delete($this->get($packagesId));
    }
}

