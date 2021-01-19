<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mazyl\Inpost\Controller\Adminhtml\Packages;

class Select extends \Magento\Backend\App\Action
{

    protected $resultPageFactory;
    protected $jsonHelper;
    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    private $orderRepository;
    /**
     * @var \Mazyl\Inpost\Model\PackagesFactory
     */
    private $packagesFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Mazyl\Inpost\Model\PackagesFactory $packagesFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Mazyl\Inpost\Model\PackagesFactory $packagesFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->logger = $logger;
        parent::__construct($context);
        $this->orderRepository = $orderRepository;
        $this->packagesFactory = $packagesFactory;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        $order = $this->orderRepository->get($post['order_id']);

        $packages = $this->packagesFactory->create();
        $package = $packages->load($post['package']);

        $setPackage["id"] = $package->getId();
        $setPackage["width"] = $package->getWidth();
        $setPackage["height"] = $package->getHeight();
        $setPackage["depth"] = $package->getDepth();
        $order->getShippingAddress()->setData("inpost_package", json_encode($setPackage));
        $order->getShippingAddress()->setData("inpost_point", $post['inpost_point']);
        $order->getShippingAddress()->save();

        try {
            return $this->jsonResponse(["message" => "Zapisano rozmiar paczki"]);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->jsonResponse($e->getMessage());
        }
    }

    /**
     * Create json response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }
}
