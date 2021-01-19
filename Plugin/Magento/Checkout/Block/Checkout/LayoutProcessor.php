<?php


namespace Mazyl\Inpost\Plugin\Magento\Checkout\Block\Checkout;

class LayoutProcessor
{

    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject, array $jsLayout
    ) {
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['street'] = [
            'component' => 'Magento_Ui/js/form/components/group',
            'dataScope' => 'shippingAddress.street',
            'provider' => 'checkoutProvider',
            'additionalClasses' => 'street',
            'sortOrder' => 60,
            'type' => 'group',
            'children' => [
                [
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'config' => [
                        'customScope' => 'shippingAddress',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'ui/form/element/input'
                    ],
                    'dataScope' => '0',
                    'label' => __('Street'),
                    'provider' => 'checkoutProvider',
                    'validation' => ['required-entry' => true, 'max_text_length' => 30],
                ],
                [
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'config' => [
                        'customScope' => 'shippingAddress',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'ui/form/element/input'
                    ],
                    'dataScope' => '1',
                    'label' => __('Nr domu'),
                    'provider' => 'checkoutProvider',
                    'validation' => ['required-entry' => true, 'max_text_length' => 5],
                ],
                [
                    'component' => 'Magento_Ui/js/form/element/abstract',
                    'config' => [
                        'customScope' => 'shippingAddress',
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'ui/form/element/input'
                    ],
                    'dataScope' => '2',
                    'label' => __('Nr lokalu'),
                    'provider' => 'checkoutProvider',
                    'validation' => ['required-entry' => false, 'max_text_length' => 5],
                ]
            ]
        ];

        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['payments-list']['children']
        )) {
            foreach ($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                     ['payment']['children']['payments-list']['children'] as $key => $payment) {
                $method = substr($key, 0, -5);

                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']['street'] = [
                    'component' => 'Magento_Ui/js/form/components/group',
                    'dataScope' => 'billingAddress' . $method . '.street',
                    'provider' => 'checkoutProvider',
                    'additionalClasses' => 'street',
                    'sortOrder' => 60,
                    'type' => 'group',
                    'children' => [
                        [
                            'component' => 'Magento_Ui/js/form/element/abstract',
                            'config' => [
                                'customScope' => 'billingAddress' . $method,
                                'template' => 'ui/form/field',
                                'elementTmpl' => 'ui/form/element/input',
                            ],
                            'dataScope' => 0,
                            'label' => __('Street'),
                            'provider' => 'checkoutProvider',
                            'validation' => ['required-entry' => true, 'max_text_length' => 30],
                        ],
                        [
                            'component' => 'Magento_Ui/js/form/element/abstract',
                            'config' => [
                                'customScope' => 'billingAddress' . $method,
                                'template' => 'ui/form/field',
                                'elementTmpl' => 'ui/form/element/input',
                            ],
                            'dataScope' => 1,
                            'label' => __('No. house'),
                            'provider' => 'checkoutProvider',
                            'validation' => ['required-entry' => true, 'max_text_length' => 5],
                        ],
                        [
                            'component' => 'Magento_Ui/js/form/element/abstract',
                            'config' => [
                                'customScope' => 'billingAddress' . $method,
                                'template' => 'ui/form/field',
                                'elementTmpl' => 'ui/form/element/input',
                            ],
                            'dataScope' => 2,
                            'label' => __('No. homes'),
                            'provider' => 'checkoutProvider',
                            'validation' => ['required-entry' => false, 'max_text_length' => 5],
                        ],
                    ],
                ];
            }
        }
        return $jsLayout;
    }
}





