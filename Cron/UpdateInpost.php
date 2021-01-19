<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mazyl\Inpost\Cron;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Mazyl\Inpost\Helper\Data;
use Magento\Sales\Model\Order\Shipment\TrackFactory;

class UpdateInpost
{

    protected $logger;

    /**
     * @var ShipmentRepositoryInterface
     */
    protected $shipmentRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var TrackFactory
     */
    private $trackFactory;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param Data $helper
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TrackFactory $trackFactory
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        Data $helper,
        ShipmentRepositoryInterface $shipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TrackFactory $trackFactory
    )
    {
        $this->shipmentRepository = $shipmentRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
        $this->helper = $helper;
        $this->trackFactory = $trackFactory;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $this->logger->addInfo("Cronjob UpdateInpost is executed.");
        $this->getLatestShipment();
    }


    /**
     * Shipment by Order id
     *
     * @param int $orderId
     * @return ShipmentInterface[]|null |null
     */
    public function getLatestShipment()
    {
        $now = new \DateTime();
        $now->modify('- 3 hour');
        $searchCriteria = $this->searchCriteriaBuilder->create()->addFieldToFilter('created_at', ['lteq' => $now->format('Y-m-d H:i:s')]);

        try {
            $shipments = $this->shipmentRepository->getList($searchCriteria);
            $shipmentRecords = $shipments->getItems();
            foreach($shipmentRecords as $singleShipment) {
                $tracksCollection = $singleShipment->getTracksCollection();
                if(empty($tracksCollection)) {
                    $shippingAddress = $singleShipment->getShippingAddress();
                    $inpost_id = $shippingAddress->getData("inpost_id");
                    if($inpost_id) {
                        $result = $this->helper->getShipmentData($inpost_id);
                        if($result) {
                            if($result->tracking_number) {
                                $data = array(
                                    'carrier_code' => 'inpostpaczkomaty',
                                    'title' => 'Inpost Paczkomaty',
                                    'number' => $result->tracking_number, // Replace with your tracking number
                                );

                                $track = $this->trackFactory->create()->addData($data);
                                $singleShipment->addTrack($track)->save();

                                $this->logger->addInfo("Update Shipment: ".$singleShipment->getIncrementId());
                            }
                        }
                    }
                }
            }

        } catch (\Exception $exception)  {
            $this->logger->critical($exception->getMessage());
            $shipmentRecords = null;
        }
        return $shipmentRecords;
    }
}

