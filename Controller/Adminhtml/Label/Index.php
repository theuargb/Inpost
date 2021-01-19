<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mazyl\Inpost\Controller\Adminhtml\Label;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Model\OrderRepository;
use Mazyl\Inpost\Model\PackagesFactory;
use Psr\Log\LoggerInterface;

class Index extends Action
{

    protected $resultPageFactory;
    protected $jsonHelper;
    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * @var PackagesFactory
     */
    private $packagesFactory;
    /**
     * @var \Mazyl\Inpost\Helper\Data
     */
    private $helper;
    /**
     * @var RawFactory
     */
    protected $resultRawFactory;
    /**
     * @var FileFactory
     */
    protected $fileFactory;
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Constructor
     *
     * @param Context $context
     * @param RawFactory $resultRawFactory
     * @param FileFactory $fileFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Mazyl\Inpost\Helper\Data $helper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        RawFactory $resultRawFactory,
        FileFactory $fileFactory,
        \Magento\Framework\App\Request\Http $request,
        \Mazyl\Inpost\Helper\Data $helper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->resultRawFactory      = $resultRawFactory;
        $this->fileFactory           = $fileFactory;
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
    }


    public function execute()
    {
        $label = $this->request->getParam('label');

        $labelData = $this->helper->getLabel($label);

        $fileType = $this->getConfig("shipping/inpost/label_format");

        $fileName = 'shipment_'.$label.'.'.$fileType;
        $this->fileFactory->create(
            $fileName,
            $labelData,
            DirectoryList::VAR_DIR,
            'application/'.$fileType
        );
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw;
    }

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

}
