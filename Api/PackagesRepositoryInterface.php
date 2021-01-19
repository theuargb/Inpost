<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mazyl\Inpost\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface PackagesRepositoryInterface
{

    /**
     * Save Packages
     * @param \Mazyl\Inpost\Api\Data\PackagesInterface $packages
     * @return \Mazyl\Inpost\Api\Data\PackagesInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Mazyl\Inpost\Api\Data\PackagesInterface $packages
    );

    /**
     * Retrieve Packages
     * @param string $packagesId
     * @return \Mazyl\Inpost\Api\Data\PackagesInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($packagesId);

    /**
     * Retrieve Packages matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Mazyl\Inpost\Api\Data\PackagesSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Packages
     * @param \Mazyl\Inpost\Api\Data\PackagesInterface $packages
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Mazyl\Inpost\Api\Data\PackagesInterface $packages
    );

    /**
     * Delete Packages by ID
     * @param string $packagesId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($packagesId);
}

