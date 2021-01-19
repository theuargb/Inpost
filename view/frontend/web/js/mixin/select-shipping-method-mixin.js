define([
    'jquery',
    'mage/translate'
], function ($, $t) {
    'use strict';
    var $last = false;

    return function (target) {
        return function (shippingMethod) {
            if($last != false && $last != null) {
                if ($last.carrier_code != shippingMethod.carrier_code && $last.method_code != shippingMethod.method_code) {
                    $("#select_point").html($t('Wybierz Paczkomat'));
                    $("#select_point_cod").html($t('Wybierz Paczkomat'));
                    $('.inpost_point_name').val("");
                }
            }

            $last = shippingMethod;
            target(shippingMethod);
        };
    }
});
