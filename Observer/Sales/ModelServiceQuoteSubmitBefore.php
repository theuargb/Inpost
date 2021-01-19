<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mazyl\Inpost\Observer\Sales;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ModelServiceQuoteSubmitBefore implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Mazyl\Inpost\Model\PackagesFactory
     */
    private $packagesFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        \Mazyl\Inpost\Model\PackagesFactory $packagesFactory,
        ScopeConfigInterface $scopeConfig
    )
    {

        $this->packagesFactory = $packagesFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getData('order');

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getData('quote');
        $shippingAddressData = $quote->getShippingAddress()->getData();


        $order->setAdditionalAddress( $quote->getAdditionalAddress() );

        if (isset($shippingAddressData['inpost_point'])) {
            $order->getShippingAddress()->setData("inpost_point", $shippingAddressData['inpost_point']);

            $defaultPackage = $this->scopeConfig->getValue('shipping/inpost/default_package', ScopeInterface::SCOPE_STORE);

            $packages = $this->packagesFactory->create();
            $package = $packages->load($defaultPackage);

            $setPackage["id"] = $package->getId();
            $setPackage["width"] = $package->getWidth();
            $setPackage["height"] = $package->getHeight();
            $setPackage["depth"] = $package->getDepth();
            $order->getShippingAddress()->setData("inpost_package", json_encode($setPackage));

        }

        return $this;
    }
}

