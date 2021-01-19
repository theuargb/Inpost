define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'mage/storage',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/model/payment/method-converter',
    'Magento_Checkout/js/model/resource-url-manager',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/action/select-billing-address',
    'mage/translate',
        'Magento_Ui/js/model/messageList'
],
        function ($, quote, storage, paymentService, methodConverter, resourceUrlManager, fullScreenLoader, errorProcessor, selectBillingAddressAction, $t, messageList) {
            'use strict';
            return function (target) {

                var saveShippingInformationParent = target.saveShippingInformation;

                target.saveShippingInformation = function () {
                    if (typeof quote.shippingMethod() === 'undefined' || quote.shippingMethod() == null) {
                        return false;
                    }
                   if (quote.shippingMethod().method_code === "inpostpaczkomatypobranie" || quote.shippingMethod().method_code === "inpostpaczkomaty") {
                       if (!$('.inpost_point_name').val()) {
                           fullScreenLoader.stopLoader();
                           messageList.addErrorMessage({ message: $t('Proszę wybrać punkt odbioru') });
                           $('html,body').animate({scrollTop: $('#checkoutSteps').offset().top - 250 }, 400);
                           return false;
                       }
                   }

                    if (!quote.billingAddress()) {
                        selectBillingAddressAction(quote.shippingAddress());
                    }

                    var result = saveShippingInformationParent.apply();

                    fullScreenLoader.startLoader();

                    var payload = {
                        addressInformation: {
                            shipping_address: quote.shippingAddress(),
                            billing_address: quote.billingAddress(),
                            shipping_method_code: quote.shippingMethod().method_code,
                            shipping_carrier_code: quote.shippingMethod().carrier_code,
                        }
                    };

                    return storage.post(
                            resourceUrlManager.getUrlForSetShippingInformation(quote),
                            JSON.stringify(payload)
                            ).done(
                            function (response) {
                                quote.setTotals(response.totals);
                                paymentService.setPaymentMethods(methodConverter(response['payment_methods']));
                                fullScreenLoader.stopLoader();
                            }
                    ).fail(
                            function (response) {
                                errorProcessor.process(response);
                                fullScreenLoader.stopLoader();
                            }
                    );

                    return result;

                }
                return target;
            };
        });
