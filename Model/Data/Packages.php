<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mazyl\Inpost\Model\Data;

use Mazyl\Inpost\Api\Data\PackagesInterface;

class Packages extends \Magento\Framework\Api\AbstractExtensibleObject implements PackagesInterface
{

    /**
     * Get packages_id
     * @return string|null
     */
    public function getPackagesId()
    {
        return $this->_get(self::PACKAGES_ID);
    }

    /**
     * Set packages_id
     * @param string $packagesId
     * @return \Mazyl\Inpost\Api\Data\PackagesInterface
     */
    public function setPackagesId($packagesId)
    {
        return $this->setData(self::PACKAGES_ID, $packagesId);
    }

    /**
     * Get name
     * @return string|null
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * Set name
     * @param string $name
     * @return \Mazyl\Inpost\Api\Data\PackagesInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Mazyl\Inpost\Api\Data\PackagesExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Mazyl\Inpost\Api\Data\PackagesExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Mazyl\Inpost\Api\Data\PackagesExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}

