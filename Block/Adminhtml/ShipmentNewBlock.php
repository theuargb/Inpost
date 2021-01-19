<?php


namespace Mazyl\Inpost\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Backend\Model\UrlInterface;

class ShipmentNewBlock extends Template
{

    /**
     * @var \Magento\Sales\Model\OrderRepository
     */
    public $orderRepository;
    /**
     * @var \Mazyl\Inpost\Model\PackagesFactory
     */
    private $packagesFactory;
    /**
     * @var UrlInterface
     */
    protected $backendUrl;

    /**
     * ShipmentNewBlock constructor.
     * @param Template\Context $context
     * @param array $data
     * @param JsonHelper|null $jsonHelper
     * @param DirectoryHelper|null $directoryHelper
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param UrlInterface $backendUrl
     * @param \Mazyl\Inpost\Model\PackagesFactory $packagesFactory
     */
    public function __construct(
        Template\Context $context,
        array $data = [],
        JsonHelper $jsonHelper = null,
        DirectoryHelper $directoryHelper = null,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        UrlInterface $backendUrl,
        \Mazyl\Inpost\Model\PackagesFactory $packagesFactory
    ){
        parent::__construct($context, $data);
        $this->orderRepository = $orderRepository;
        $this->packagesFactory = $packagesFactory;
        $this->backendUrl = $backendUrl;
    }

    /**
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOrder() {

        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->orderRepository->get($orderId);

        return $order;
    }

    public function isInpostShippingMethod()
    {
        $order = $this->getOrder();

        if($order->getShippingMethod() == "inpostpaczkomatykurier_inpostpaczkomatykurier"
            || $order->getShippingMethod() == "inpostpaczkomaty_inpostpaczkomaty"
            || $order->getShippingMethod() == "inpostpaczkomatypobranie_inpostpaczkomatypobranie") return true;

        return false;
    }

    public function getPackages() {
        $packages = $this->packagesFactory->create();
        $collection = $packages->getCollection();

        return $collection;
    }

    public function packagesUrl() {
        $url = $this->backendUrl->getUrl(
            'mazyl_inpost/packages/select'
        );
        return $url;
    }

}
