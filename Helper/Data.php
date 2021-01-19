<?php


namespace Mazyl\Inpost\Helper;


use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use Monolog\Handler\StreamHandler;
use stdClass;

class Data extends AbstractHelper
{

    const PRODUCTION_URL = 'https://api-shipx-pl.easypack24.net/v1/';

    const SANDBOX_URL = 'https://sandbox-api-shipx-pl.easypack24.net/v1/';


    protected $_organization_id;
    protected $curl;
    protected $dir;
    protected $orderRepository;
    protected $_accessToken;

    /**
     * Data constructor.
     * @param Context $context
     * @param DirectoryList $dir
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Context $context,
        DirectoryList $dir,
        OrderRepositoryInterface $orderRepository
    )
    {
        $this->orderRepository = $orderRepository;
        $this->dir = $dir;

        parent::__construct($context);

        $this->init();
    }

    public function init()
    {
        $this->_organization_id = $this->getConfig('organization_id');
        $this->_accessToken = $this->getConfig('access_token');
    }


    /**
     * @param $path
     * @param bool $data
     * @param bool $file
     * @param bool $custom
     * @return mixed
     */
    private function doRequest(
        $path,
        $data = false,
        $file = false,
        $custom = false
    )
    {

        $service_url = ($this->isSandbox()) ? self::SANDBOX_URL : self::PRODUCTION_URL . $path;
        $this->curl = curl_init($service_url);

        if($data) {
            curl_setopt($this->curl, CURLOPT_POST, true);
            $post_data = json_encode($data);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post_data);
        }
        if($file) {
            curl_setopt($this->curl, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($this->curl, CURLINFO_CONTENT_TYPE, $file);
        }
        if($custom) {
            curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $custom);
        }
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 10);
        $authorization = "Authorization:Bearer " . $this->_accessToken;
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $authorization));


        $curl_response = curl_exec($this->curl);
        if (curl_errno($this->curl)) {
            $this->log(curl_error($this->curl));
            return false;
        }

        if(!$file) {
            $response = json_decode($curl_response);
        } else {
            $response = $curl_response;
        }
        curl_close($this->curl);

        if(isset($response->error)) {
            $this->log($response);
        }

        return $response;
    }



    /**
     * @param $name
     * @return mixed
     */
    public function getPointData($name)
    {
        $path = 'points/' . $name;
        $result = $this->doRequest($path);
        return $result;
    }

    public function getAllStatuses()
    {
        $path = 'statuses';
        return $this->doRequest($path);
    }

    public function getStatusLabel($status)
    {
        $result = $this->getAllStatuses();
        foreach($result->items as $item) {
            if($status == $item->name) {
                return $item->title;
            }
        }
        return $status;
    }

    /**
     * @param $data
     */
    private function log($data)
    {
        $this->_logger->pushHandler(new StreamHandler($this->dir->getRoot().'/var/log/paczkomaty.log'));
        $this->_logger->error(print_r($data,true));
    }


    public function getConfig($name)
    {
        return $this->scopeConfig->getValue('shipping/inpost/'.$name, ScopeInterface::SCOPE_STORE);
    }

    public function isSandbox()
    {
        return $this->scopeConfig->isSetFlag('shipping/inpost/sandbox', ScopeInterface::SCOPE_STORE);
    }



    public function prepareShipments(
        $orderId,
        $phone,
        $email,
        $reference,
        $parcel,
        $targetPoint,
        $sendingMethod,
        $address = false,
        $dropoffPoint = false,
        $cod = false
    )
    {
        $phone = substr(preg_replace("/[^0-9]/", "", $phone),-9);
        $data = array(
            'receiver' => array(
                'phone' => $phone,
                'email' => $email,
                'address' => $address
            )
        ,'parcels' => array(
                $parcel
            )
        ,'service' => 'inpost_locker_standard'
        ,'reference' => $reference
        ,'only_choice_of_offer' => !$this->getConfig('shipping/inpost/auto_buy')
        ,'custom_attributes' => array(
                'target_point' => $targetPoint
            ,'sending_method' => $sendingMethod
            )
        );

        if($dropoffPoint) {
            $data['custom_attributes']['dropoff_point'] = $dropoffPoint;
        }

        if($cod) {
            $data['cod'] = array(
                'amount' => $cod
            ,'currency' => 'PLN'
            );
        }

        return $data;
    }

    public function sendShipments($shipemnts)
    {
        $path = 'organizations/' . $this->_organization_id . '/shipments/';

        return $this->doRequest($path, $shipemnts);
    }


    public function getSizeLabel($size)
    {
        $sizes = [
            'small' => __('Gabaryt A')
            ,'medium' => __('Gabaryt B')
            ,'large' => __('Gabaryt C')
        ];

        return $sizes[$size];
    }

    public function getSizeShortLabel($size)
    {
        $label = $this->getSizeLabel($size);
        $label = str_replace('Gabaryt ','',$label);

        return $label;
    }

    public function getSendingMethods()
    {
        $path = 'sending_methods?service=inpost_locker_standard';
        $requestResult = $this->doRequest($path);
        $result = array();
        foreach($requestResult->items as $item) {
            $result[$item->id] = $item->name;
        }
        return $result;
    }

    public function getTracking($trackingNumber)
    {
        $path = 'tracking/' . $trackingNumber;
        $requestResult = $this->doRequest($path);
        return $requestResult;
    }

    public function getLabel($shipmentIds)
    {
        if(!is_array($shipmentIds))
        {
            $shipmentIds = array($shipmentIds);
        }
        $labelFormat = $this->getFormatLabel();
        $type = 'normal';
        if ($labelFormat != 'pdf')
        {
            $type = 'a6';
        }
        $path = 'organizations/' . $this->_organization_id . '/shipments/labels';
        $data = ['type' => $type, 'format' => $labelFormat, 'shipment_ids' => $shipmentIds];
        $requestResult = $this->doRequest($path, $data, 'application/pdf');
        return $requestResult;
    }

    public function buyShipment($shipmentId, $offerId)
    {
        $path = 'shipments/' . $shipmentId . '/buy';
        $data= new stdClass();
        $data->offer_id = $offerId;
        $requestResult = $this->doRequest($path, $data);
        return $requestResult;
    }

    public function cancelShipment($shipmentId)
    {
        $path = 'shipments/' . $shipmentId;
        $requestResult = $this->doRequest($path,false, false, 'DELETE');
        return $requestResult;
    }

    public function getShipmentData($shipmentId)
    {
        $path = 'shipments/' . $shipmentId;
        $requestResult = $this->doRequest($path);
        return $requestResult;
    }

    public function getCanGetShipmentLabel($status)
    {
        $forbiddenStatuses = [
            'created', 'offers_prepared', 'offer_selected', 'canceled'
        ];
        return !in_array($status,$forbiddenStatuses);
    }

    public function getCanBuyShipment($status)
    {
        $allowedStatuses = [
            'offer_selected'
        ];
        return in_array($status,$allowedStatuses);
    }

    public function getCanCancelShipment($status)
    {
        $allowedStatuses = [
            'offers_prepared', 'created', 'offer_selected'
        ];
        return in_array($status,$allowedStatuses);
    }

    public function isInpostShippingMethod($orderId)
    {
        $order = $this->orderRepository->get($orderId);

        if (strpos($order->getShippingMethod(),'smpaczkomaty2') !== false)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function getFormatLabel()
    {
        return $this->getConfig('label_format');
    }

    public function getExtension()
    {
        $labelFormat = $this->getFormatLabel();
        switch ($labelFormat)
        {
            case 'pdf':
                return 'pdf';
            default:
                return 'epl';
        }
    }
}
