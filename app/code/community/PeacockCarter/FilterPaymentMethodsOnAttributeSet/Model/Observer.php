<?php

/**
 * PeacockCarter
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 * @category    PeacockCarter
 * @package     FilterPaymentMethodsOnAttributeSet
 * @copyright   Copyright (c) 2016 PeacockCarter Ltd
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PeacockCarter_FilterPaymentMethodsOnAttributeSet_Model_Observer
{
    /**
     * @array
     */
    private $_restrictedAttributeSets;

    /**
     * @var
     */
    private $_attributeSetsInCart;

    /**
     * @var Mage_Sales_Model_Quote
     */
    private $quote;

    /**
     * @param Varien_Event_Observer $observer
     */
    public function filterPaymentMethod(Varien_Event_Observer $observer)
    {
        $paymentMethodCode = $this->getPaymentMethodCode($observer);

        if (!$this->isPaymentFilterEnabled($paymentMethodCode) || !$this->doesQuoteExist($observer)) {

            return;
        }

        $this->setRestrictedAttributeSetsForMethod($paymentMethodCode);

        if (count($this->_restrictedAttributeSets) > 0) {
            $this->setAllAttributeSetsInCart();
            $this->setMethodVisibility($observer);
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return mixed
     */
    private function getPaymentMethodCode(Varien_Event_Observer $observer)
    {
        $MethodInstance    = $observer->getEvent()->getMethodInstance();
        $paymentMethodCode = $MethodInstance->getCode();

        return $paymentMethodCode;
    }

    /**
     * @param $paymentMethodCode
     *
     * @return bool
     */
    private function isPaymentFilterEnabled($paymentMethodCode)
    {
        $enabled = Mage::getStoreConfig(
            'payment/' . $paymentMethodCode . '/attribute_sets_filter',
            Mage::app()->getStore()
        );

        return isset($enabled) && $enabled === "1";
    }

    /**
     * @param Varien_Event_Observer $observer
     *
     * @return bool
     */
    private function doesQuoteExist(Varien_Event_Observer $observer)
    {
        $this->quote = $observer->getEvent()->getQuote();

        return $this->quote && $this->quote->getId();
    }

    /**
     * @param $paymentMethodCode
     */
    protected function setRestrictedAttributeSetsForMethod($paymentMethodCode)
    {
        $attributeSets = Mage::getStoreConfig(
            'payment/' . $paymentMethodCode . '/attribute_sets_to_filter',
            Mage::app()->getStore()
        );

        $this->_restrictedAttributeSets = explode(',', $attributeSets);
    }

    private function setAllAttributeSetsInCart()
    {
        $this->_attributeSetsInCart = '';

        $products = $this->getProductsInCart();

        foreach ($products as $product) {

            $this->addAttributeSetToArray($product->getProduct()->getAttributeSetId());
        }
    }

    /**
     * @return array
     */
    private function getProductsInCart()
    {
        return $this->quote->getAllVisibleItems();
    }

    /**
     * @param $attributeSetId
     */
    private function addAttributeSetToArray($attributeSetId)
    {
        $this->_attributeSetsInCart .= $attributeSetId . ',';
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    private function setMethodVisibility(Varien_Event_Observer $observer)
    {
        $result = $observer->getEvent()->getResult();
        if ($result->isAvailable) {
            $result->isAvailable = $this->isAttributeSetAllowed();
        }
    }

    /**
     * @return bool
     */
    private function isAttributeSetAllowed()
    {
        $attributeSets                 = explode(',', $this->_attributeSetsInCart);
        $restrictedAttributeSetsInCart = array_intersect($attributeSets, $this->_restrictedAttributeSets);

        return count($restrictedAttributeSetsInCart) === 0;
    }
}