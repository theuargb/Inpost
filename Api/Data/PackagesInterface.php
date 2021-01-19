<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mazyl\Inpost\Api\Data;

interface PackagesInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const PACKAGES_ID = 'packages_id';
    const NAME = 'name';

    /**
     * Get packages_id
     * @return string|null
     */
    public function getPackagesId();

    /**
     * Set packages_id
     * @param string $packagesId
     * @return \Mazyl\Inpost\Api\Data\PackagesInterface
     */
    public function setPackagesId($packagesId);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Mazyl\Inpost\Api\Data\PackagesInterface
     */
    public function setName($name);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Mazyl\Inpost\Api\Data\PackagesExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Mazyl\Inpost\Api\Data\PackagesExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Mazyl\Inpost\Api\Data\PackagesExtensionInterface $extensionAttributes
    );
}

