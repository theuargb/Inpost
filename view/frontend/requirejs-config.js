var config = {

    paths: {
        'paczkomatyJs': 'https://geowidget.easypack24.net/js/sdk-for-javascript'
    },
    config: {
        "mixins": {
            "Magento_Checkout/js/model/shipping-save-processor/default": {
                "Mazyl_Inpost/js/view/checkout/shipping/shipping-save-processor-mixin": true
            },
            'Magento_Checkout/js/action/select-shipping-method': {
                'Mazyl_Inpost/js/mixin/select-shipping-method-mixin': true
            },
            'Magento_Checkout/js/action/set-shipping-information': {
                'Mazyl_Inpost/js/mixin/set-shipping-information-mixin': true
            }
        }
    }
};
