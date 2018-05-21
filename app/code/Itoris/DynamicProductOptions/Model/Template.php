<?php
/**
 * ITORIS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ITORIS's Magento Extensions License Agreement
 * which is available through the world-wide-web at this URL:
 * http://www.itoris.com/magento-extensions-license.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@itoris.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extensions to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to the license agreement or contact sales@itoris.com for more information.
 *
 * @category   ITORIS
 * @package    ITORIS_M2_DYNAMIC_PRODUCT_OPTIONS
 * @copyright  Copyright (c) 2016 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */

namespace Itoris\DynamicProductOptions\Model;

class Template extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct() {
        $this->_init('Itoris\DynamicProductOptions\Model\ResourceModel\Template');
    }

    public function getSections() {
        $sections = [];
        if ($this->getConfiguration()) {
            $sections = \Zend_Json::decode($this->getConfiguration());
        }
        return $sections;
    }

    public function getFormStyle() {
        if ($this->getId()) {
            return $this->getData('form_style');
        }
        return 'table_sections';
    }

    public function getAppearance() {
        if ($this->getId()) {
            return $this->getData('appearance');
        }
        return 'on_product_view';
    }
}