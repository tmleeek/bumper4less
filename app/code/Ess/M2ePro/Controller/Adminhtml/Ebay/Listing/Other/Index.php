<?php

namespace Ess\M2ePro\Controller\Adminhtml\Ebay\Listing\Other;

class Index extends \Ess\M2ePro\Controller\Adminhtml\Ebay\Listing\Other
{
    public function execute()
    {
        $this->addContent($this->createBlock('Ebay\Listing\Other'));
        $this->getResultPage()->getConfig()->getTitle()->prepend($this->__('3rd Party Listings'));
        $this->setPageHelpLink('x/7AEtAQ');

        return $this->getResult();
    }
}