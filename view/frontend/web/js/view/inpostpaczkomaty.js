define(
        [
            "jquery",
            'uiComponent',
            'Magento_Checkout/js/model/shipping-rates-validator',
            'Magento_Checkout/js/model/shipping-rates-validation-rules',
            '../model/shipping-rates-validator/inpost',
            '../model/shipping-rates-validation-rules/inpost',
            'Magento_Checkout/js/model/quote',
            'mage/translate',
            'Magento_Checkout/js/model/shipping-service'
        ],
        function (
                $,
                Component,
                defaultShippingRatesValidator,
                defaultShippingRatesValidationRules,
                shippingRatesValidator,
                shippingRatesValidationRules,
                quote,
                $t,
                shippingService
                ) {
            'use strict';
            defaultShippingRatesValidator.registerValidator('inpostpaczkomaty', shippingRatesValidator);
            defaultShippingRatesValidationRules.registerRules('inpostpaczkomaty', shippingRatesValidationRules);

            quote.shippingMethod.subscribe(function () {

                var $label = $('#label_carrier_inpostpaczkomaty_inpostpaczkomaty');
                console.log($('#select_point').length);
                if ($label.length && $('#select_point').length === 0) {

                    $label.html($label.text() + '<br/><a href="#" id="select_point" class="select_point action">' + $t('Wybierz Paczkomat') + '</a>');
                    $('.inpost_point_name').val('');
                }
                var $label2 = $('#label_carrier_inpostpaczkomatypobranie_inpostpaczkomatypobranie');
                if ($label2.length && $('#select_point_cod').length === 0) {
                    $label2.html($label2.text() + '<br/><a href="#" id="select_point_cod" class="select_point_cod action">' + $t('Wybierz Paczkomat') + '</a>');
                    $('.inpost_point_name').val('');
                }
                //$('.inpost_point_name').val('');
            });

            shippingService.isLoading.subscribe(function (isLoading) {

                if (isLoading) {
                    return
                }


                var $label = $('#label_carrier_inpostpaczkomaty_inpostpaczkomaty');
                if ($label.length && $('#select_point').length === 0) {
                    $label.html($label.text() + '<br/><a href="#" id="select_point" class="select_point action">' + $t('Wybierz Paczkomat') + '</a>');
                    $('.inpost_point_name').val('');
                }
                var $label2 = $('#label_carrier_inpostpaczkomatypobranie_inpostpaczkomatypobranie');
                if ($label2.length && $('#select_point_cod').length === 0) {
                    $label2.html($label2.text() + '<br/><a href="#" id="select_point_cod" class="select_point_cod action">' + $t('Wybierz Paczkomat') + '</a>');
                    $('.inpost_point_name').val('');
                }

            });


            return Component;
        }
);
