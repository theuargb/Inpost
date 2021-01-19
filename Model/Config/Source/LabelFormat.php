<?php

    namespace Mazyl\Inpost\Model\Config\Source;

    class LabelFormat implements \Magento\Framework\Option\ArrayInterface
    {
        /**
         * Options getter
         *
         * @return array
         */
        public function toOptionArray()
        {
            return [
                ['value' => 'pdf', 'label' => __('Pdf')]
                ,['value' => 'epl', 'label' => __('Epl')]
                ,['value' => 'zpl', 'label' => __('Zpl')]
            ];
        }

        /**
         * Get options in "key-value" format
         *
         * @return array
         */
        public function toArray()
        {
            return [
                'pdf' => __('Pdf')
                ,'epl' => __('Epl')
                ,'zpl' => __('Zpl')
            ];
        }
    }
