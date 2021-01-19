/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper, quote) {
    'use strict';

    return function (setShippingInformationAction) {

        return wrapper.wrap(setShippingInformationAction, function (originalAction) {
            var shippingAddress = quote.shippingAddress();
            if (shippingAddress['extension_attributes'] === undefined) {
                shippingAddress['extension_attributes'] = {};
            }

            if(shippingAddress.customAttributes && shippingAddress.customAttributes.hasOwnProperty("additional_address")) {
                shippingAddress['extension_attributes']['additional_address'] = shippingAddress.customAttributes['additional_address'];
            }

            return originalAction();
        });
    };
});
