<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mazyl\Inpost\Model\Carrier;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class InpostPaczkomaty extends AbstractCarrier implements
    CarrierInterface
{

    protected $_code = 'inpostpaczkomaty';

    protected $_isFixed = true;

    protected $_rateResultFactory;

    protected $_rateMethodFactory;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param StoreManagerInterface $storeManager
     * @param MethodFactory $rateMethodFactory
     * @param Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        StoreManagerInterface $storeManager,
        MethodFactory $rateMethodFactory,
        Session $checkoutSession,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
        $this->storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * {@inheritdoc}
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $shippingPrice = $this->getConfigData('price');

        $result = $this->_rateResultFactory->create();

        if ($shippingPrice !== false) {
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod($this->_code);
            $method->setMethodTitle($this->getConfigData('name'));

            if ($request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes()) {
                $shippingPrice = '0.00';
            }
            if($this->getConfigData('free_shipping') > 0) {
                $quote = $this->checkoutSession->getQuote();
                $discountTotal = 0;
                foreach ($quote->getAllItems() as $item){
                    $discountTotal += $item->getDiscountAmount();
                }
                $amountTotal = $quote->getGrandTotal() - $quote->getShippingAmount() - $discountTotal;
                if($amountTotal >= $this->getConfigData('free_shipping')) {
                    $shippingPrice = "0.00";
                }
            }

            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);

            $result->append($method);
        }

        return $result;
    }

    public function getCurrentUrl() {
        return $this->storeManager->getStore()->getCurrentUrl();
    }

    /**
     * getAllowedMethods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * Check if carrier has shipping tracking option available
     *
     * All \Magento\Usa carriers have shipping tracking option available
     *
     * @return boolean
     */
    public function isTrackingAvailable()
    {
        return true;
    }
}
