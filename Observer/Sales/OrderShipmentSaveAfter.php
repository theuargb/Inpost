<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mazyl\Inpost\Observer\Sales;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\Order\Shipment\TrackFactory;
use Magento\Store\Model\ScopeInterface;
use Mazyl\Inpost\Helper\Data;

class OrderShipmentSaveAfter implements ObserverInterface
{
    /**
     * @var Magento\Sales\Model\Order\Shipment\TrackFactory
     */
    private $trackFactory;
    /**
     * @var Mazyl\Inpost\Helper\Data
     */
    private $helper;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * OrderShipmentSaveAfter constructor.
     * @param TrackFactory $trackFactory
     * @param Data $helper
     * @param ScopeConfigInterface $scopeConfig
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        TrackFactory $trackFactory,
        Data $helper,
        ScopeConfigInterface $scopeConfig,
        ManagerInterface $messageManager
    )
    {
        $this->trackFactory = $trackFactory;
        $this->helper = $helper;
        $this->scopeConfig = $scopeConfig;
        $this->messageManager = $messageManager;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(
        Observer $observer
    ) {
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();

        if($order->getShippingMethod() == "inpostpaczkomatypobranie_inpostpaczkomatypobranie"
            || $order->getShippingMethod() == "inpostpaczkomaty_inpostpaczkomaty"
            || $order->getShippingMethod() == "inpostpaczkomatykurier_inpostpaczkomatykurier"
        ) {


            $shippingAddress = $order->getShippingAddress();
            $inpost_package = $shippingAddress->getData("inpost_package");
            $inpost_package = json_decode($inpost_package, true);

            $cod = false;
            if ($order->getShippingMethod() == "inpostpaczkomatypobranie_inpostpaczkomatypobranie") {
                $cod = $order->getGrantTotal();
            }
            $address = false;
            if($order->getShippingMethod() == "inpostpaczkomatykurier_inpostpaczkomatykurier") {

                $street = $skuList = explode(PHP_EOL, $shippingAddress->getStreet());
                $home = (isset($street[2])) ? $street[1]."/".$street[2] : $street[1];
                $address['street'] = $street[0];
                $address['building_number'] = $home;
                $address['city'] = $shippingAddress->getCity();
                $address['post_code'] = $shippingAddress->getPostcode();
                $address['country_code'] = $shippingAddress->getCountryId();
            }


            $sendingMethod = $this->scopeConfig->getValue('shipping/inpost/sending_method', ScopeInterface::SCOPE_STORE);
            $dropOff = false;
            if ($sendingMethod == "parcel_locker") {
                $dropOff = $this->scopeConfig->getValue('shipping/inpost/dropoff_point', ScopeInterface::SCOPE_STORE);
            }

            $parcel = [
                "id" => "Package ".$order->getId(),
                "dimensions" => [
                    "length" => $inpost_package['depth'],
                    "width" => $inpost_package['width'],
                    "height" => $inpost_package['height'],
                    "unit" => "mm"
                ],
                "weight" => [
                    "amount" => round($order->getWeight()),
                    "unit" => "kg"
                ]
            ];


            $shipments = $this->helper->prepareShipments(
                $order->getId(),
                $shippingAddress->getTelephone(),
                $shippingAddress->getEmail(),
                '#' . $order->getIncrementId(),
                $parcel,
                $shippingAddress->getData("inpost_point"),
                $sendingMethod,
                $address,
                $dropOff,
                $cod
            );



            $result = $this->helper->sendShipments($shipments);
            if ($result && property_exists($result, 'error')) {
                $this->messageManager->addErrorMessage(sprintf(__('Wystąpił błąd dla zamówienia %s podczas tworzenia przesyłki: %s %s'), $order->getIncrementId(), $result->message, print_r($result->details, true)));
            } elseif ($result) {
                $this->messageManager->addSuccessMessage(__('Utworzono przesyłkę dla Inpost' )); 

                if ($this->helper->getConfig('auto_buy') == 1) {
                    $this->helper->buyShipment($result->id, $result->selected_offer->id);
                    $this->messageManager->addSuccessMessage(__('Przesyłka została opłacona'));
                }

                $shippingAddress->setData("inpost_id", $result->id);
                $shippingAddress->save();

            } else {
                $this->messageManager->addErrorMessage(sprintf(__('Wystąpił błąd dla zamówienia %s podczas tworzenia przesyłki.'), $order->getIncrementId()));
            }
        }
    }
}
