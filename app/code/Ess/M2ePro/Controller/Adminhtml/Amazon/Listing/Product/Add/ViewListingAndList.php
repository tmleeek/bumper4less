<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Controller\Adminhtml\Amazon\Listing\Product\Add;

class ViewListingAndList extends \Ess\M2ePro\Controller\Adminhtml\Amazon\Listing\Product\Add
{
    //########################################

    public function execute()
    {
        $listingId = $this->getRequest()->getParam('id');

        if (empty($listingId)) {
            return $this->_redirect('*/amazon_listing/index');
        }

        return $this->_redirect('*/amazon_listing/view', array(
            'id' => $listingId,
            'do_list' => true
        ));
    }

    //########################################
}