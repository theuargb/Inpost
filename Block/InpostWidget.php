<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mazyl\Inpost\Block;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\ScopeInterface;

class InpostWidget extends Template
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * Constructor
     *
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        Session $checkoutSession,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context, $data);
    }


    public function isSandbox()
    {
        return $this->scopeConfig->isSetFlag('shipping/inpost/sandbox', ScopeInterface::SCOPE_STORE);
    }

    public function getConfig($name)
    {
        return $this->scopeConfig->getValue('shipping/inpost/'.$name, ScopeInterface::SCOPE_STORE);
    }

    public function getQuote()
    {
        return $this->checkoutSession->getQuote();
    }

    public function getInpostPointName()
    {
        $quote = $this->getQuote();

        return $quote->getShippingAddress()->getData("inpost_point");
    }

    public function getCarrierName()
    {
        $quote = $this->getQuote();

        return $quote->getShippingAddress()->getShippingMethod();
    }
}
