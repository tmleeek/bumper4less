<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Controller\Adminhtml\Amazon\Marketplace;

use Ess\M2ePro\Controller\Adminhtml\Amazon\Marketplace;

class Index extends Marketplace
{
    //########################################

    public function execute()
    {
        $this->addContent($this->createBlock('Amazon\Marketplace'));
        $this->getResult()->getConfig()->getTitle()->prepend($this->__('Marketplaces'));
        $this->setPageHelpLink('x/9AEtAQ');

        return $this->getResult();
    }

    //########################################
}