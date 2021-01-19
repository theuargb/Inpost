<?php
namespace Mazyl\Inpost\Ui\Component\Listing\Column;

use Magento\Framework\App\ResourceConnection;
use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use Magento\Sales\Model\OrderFactory;
use \Magento\Ui\Component\Listing\Columns\Column;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Backend\Model\UrlInterface;

class Label extends Column
{

    protected $_orderRepository;
    protected $_searchCriteria;
    protected $_customfactory;
    /**
     * @var UrlInterface
     */
    private $backendUrl;

    /**
     * Label constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $criteria
     * @param ResourceConnection $resource
     * @param OrderFactory $orderFactory
     * @param UrlInterface $backendUrl
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $criteria,
        ResourceConnection $resource,
        OrderFactory $orderFactory,
        UrlInterface $backendUrl,
        array $components = [], array $data = [])
    {
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteria  = $criteria;
        $this->resource = $resource;
        $this->orderFactory = $orderFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->backendUrl = $backendUrl;
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $connection  = $this->resource->getConnection();
            $tableName = $connection->getTableName('sales_shipment_grid');
            foreach ($dataSource['data']['items'] as & $item) {
                $order = $this->orderFactory->create()->loadByIncrementId($item["order_increment_id"]);
                $id = $order->getShippingAddress()->getData("inpost_id");
                if($id) {
                    $item['label'] = html_entity_decode("<a href='".$this->labelUrl($id)."' target='_blank'>".$id."</a>");
                } else {
                    $item['label'] = " - ";
                }
            }
        }
        return $dataSource;
    }

    public function labelUrl($id) {
        $url = $this->backendUrl->getUrl(
            'mazyl_inpost/label/index',
            ["label" => $id]
        );
        return $url;
    }
}
