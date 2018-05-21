<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Controller\Adminhtml\Ebay\Settings;

class Index extends \Ess\M2ePro\Controller\Adminhtml\Ebay\Settings
{
    //########################################

    protected function getLayoutType()
    {
        return self::LAYOUT_TWO_COLUMNS;
    }

    //########################################

    public function execute()
    {
        $activeTab = $this->getRequest()->getParam('active_tab', NULL);

        if (is_null($activeTab)) {
            $activeTab = \Ess\M2ePro\Block\Adminhtml\Ebay\Settings\Tabs::TAB_ID_MAIN;
        }

        /** @var \Ess\M2ePro\Block\Adminhtml\Ebay\Settings\Tabs $tabsBlock */
        $tabsBlock = $this->createBlock('Ebay\Settings\Tabs', '', ['data' => [
            'active_tab' => $activeTab
        ]]);

        if ($this->isAjax()) {
            $this->setAjaxContent(
                $tabsBlock->getTabContent($tabsBlock->getActiveTabById($activeTab))
            );

            return $this->getResult();
        }

        $this->addLeft($tabsBlock);
        $this->addContent($this->createBlock('Ebay\Settings'));

        $this->setPageHelpLink('x/0AEtAQ');

        $this->getResult()->getConfig()->getTitle()->prepend($this->__('Settings'));

        return $this->getResult();
    }

    //########################################
}