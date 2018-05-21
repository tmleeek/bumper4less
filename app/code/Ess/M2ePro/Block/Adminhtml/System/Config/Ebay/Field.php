<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Block\Adminhtml\System\Config\Ebay;

class Field extends \Ess\M2ePro\Block\Adminhtml\System\Config\Integration
{
    /**
     * @inheritdoc
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->setValue((int)$this->moduleHelper->getConfig()->getGroupValue(
            '/component/ebay/', 'mode'
        ));

        return parent::_getElementHtml($element);
    }
}