<?php


namespace Mazyl\Inpost\Model\Config\Source;


class PackageList implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Mazyl\Inpost\Model\PackagesFactory
     */
    private $packagesFactory;

    public function __construct(
        \Mazyl\Inpost\Model\PackagesFactory $packagesFactory
    )
    {

        $this->packagesFactory = $packagesFactory;
    }

    public function toOptionArray()
    {
        $return = [];

        $packages = $this->packagesFactory->create();
        $collection = $packages->getCollection();

        foreach($collection as $item) {
            $return[] = ['value' => $item->getId(), 'label' => $item->getName()];
        }
        return $return;
    }
}
