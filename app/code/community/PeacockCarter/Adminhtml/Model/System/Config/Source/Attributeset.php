<?php

/**
 * PeacockCarter
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    PeacockCarter
 * @package     AdminhtmlAttributeSetSource
 * @copyright  Copyright (c) 2016 PeacockCarter Ltd
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PeacockCarter_Adminhtml_Model_System_Config_Source_Attributeset
{
    /**
     * @var
     */
    protected $_options;

    /**
     * @param bool $isMultiSelect
     *
     * @return mixed
     */
    public function toOptionArray($isMultiSelect = false)
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter('4')
                ->loadData()
                ->toOptionArray(false);
        }

        $options = $this->_options;

        if (!$isMultiSelect) {
            array_unshift(
                $options,
                array('value' => '', 'label' => Mage::helper('adminhtml')->__('--Please Select--'))
            );
        }

        return $options;
    }
}