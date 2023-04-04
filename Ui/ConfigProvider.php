<?php
declare(strict_types=1);

namespace Mazyl\Inpost\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Mazyl\Inpost\Helper\Data;
use PayPro\Przelewy24\Api\Data\ApiPaymentMethodInterface;

class ConfigProvider implements ConfigProviderInterface
{

    /**
     * @var Data
     */
    private $helper;

    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    public function getConfig(): array
    {
        return [
            'inpost_widget' => [
                'public_api_key' => $this->helper->getConfig('geo_widget_apikey'),
                'sandbox' => $this->helper->isSandbox(),
            ]
        ];
    }
}
