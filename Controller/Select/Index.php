<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mazyl\Inpost\Controller\Select;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;

class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Data
     */
    protected $jsonHelper;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var \Mazyl\Inpost\Helper\Data
     */
    protected $helper;

    /**
     * Constructor
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $jsonHelper
     * @param Session $checkoutSession
     * @param \Mazyl\Inpost\Helper\Data $helper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $jsonHelper,
        Session $checkoutSession,
        \Mazyl\Inpost\Helper\Data $helper,
        LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->logger = $logger;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context);
        $this->helper = $helper;
    }

    /**
     * Execute view action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();

        if($post['point_name']) {
            $pointData = $this->helper->getPointData($post['point_name']);

            if($post['type'] == 1) {
                $shippingAddress = $this->getQuote()->getShippingAddress();
                $shippingAddress->setData("inpost_point", $post['point_name']);
                $shippingAddress->save();
                return $this->jsonResponse(["status" => 1, "type" => 1, "message" => __("Zapisano punkt"), "point_data" => $pointData]);
            } else {

                $pointPayment = get_object_vars($pointData->payment_type);
                if (isset($pointPayment[0])) {
                    return $this->jsonResponse(["status" => 0, "message" => __("Wybrany punkt odbioru nie obsługuje płatności przy odbiorze.")]);
                } else {
                    $shippingAddress = $this->getQuote()->getShippingAddress();
                    $shippingAddress->setData("inpost_point", $post['point_name']);
                    $shippingAddress->save();

                    return $this->jsonResponse(["status" => 1, "type" => 0, "message" => __("Zapisano punkt"), "point_data" => $pointData]);
                }

            }
        }

        return $this->jsonResponse(["status" => 0, "message" => __("Nie wybrano punktu odbioru")]);
    }

    /**
     * Create json response
     *
     * @return ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }

    public function getQuote()
    {
        return $this->checkoutSession->getQuote();
    }
}
