<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mazyl\Inpost\Api\Data;

interface PackagesSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Packages list.
     * @return \Mazyl\Inpost\Api\Data\PackagesInterface[]
     */
    public function getItems();

    /**
     * Set name list.
     * @param \Mazyl\Inpost\Api\Data\PackagesInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

