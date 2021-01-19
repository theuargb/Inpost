<?php


namespace Mazyl\Inpost\Model\Config\Source;


class SendingMethods implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
        $return = [];
        $return[] = ['value' => "parcel_locker", 'label' => __("Nadanie w Paczkomacie")];
        $return[] = ['value' => "pok", 'label' => __("Nadanie w POK")];
        $return[] = ['value' => "courier_pok", 'label' => __("Nadanie w POK - Kurier")];
        $return[] = ['value' => "branch", 'label' => __("Nadanie w Oddziale")];
        $return[] = ['value' => "dispatch_order", 'label' => __("Odbiór przez Kuriera")];
        $return[] = ['value' => "pop", 'label' => __("Nadanie w POP")];


        return $return;
    }
}
