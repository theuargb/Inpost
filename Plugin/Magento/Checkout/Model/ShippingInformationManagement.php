<?php
namespace Mazyl\Inpost\Plugin\Magento\Checkout\Model;


class ShippingInformationManagement
{
    protected $quoteRepository;

    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        $extAttributes = $addressInformation->getBillingAddress()->getExtensionAttributes();
        if($extAttributes) {
//        $extAttributes = $addressInformation->getCustomAttributes();
            $additionalAddress = $extAttributes->getAdditionalAddress();
            $quote = $this->quoteRepository->getActive($cartId);
            $quote->setAdditionalAddress($additionalAddress);
        }
    }
}
