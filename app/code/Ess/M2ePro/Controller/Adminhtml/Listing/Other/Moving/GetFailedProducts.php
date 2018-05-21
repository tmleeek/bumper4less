<?php

namespace Ess\M2ePro\Controller\Adminhtml\Listing\Other\Moving;

use Ess\M2ePro\Controller\Adminhtml\Listing;

class GetFailedProducts extends Listing
{
    public function execute()
    {
        $block = $this->createBlock(
            'Listing\Moving\FailedProducts','',
            ['data' => [
                'grid_url' => $this->getUrl('*/listing_other_moving/getFailedProductsGrid',array('_current'=>true))
            ]]
        );

        $this->setAjaxContent($block);
        return $this->getResult();
    }
}