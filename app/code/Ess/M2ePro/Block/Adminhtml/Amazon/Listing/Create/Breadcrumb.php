<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Block\Adminhtml\Amazon\Listing\Create;

class Breadcrumb extends \Ess\M2ePro\Block\Adminhtml\Widget\Breadcrumb
{
    //########################################

    public function _construct()
    {
        parent::_construct();

        // Initialization block
        // ---------------------------------------
        $this->setId('amazonListingBreadcrumb');
        // ---------------------------------------

        $this->setSteps([
            [
                'id' => 1,
                'title' => $this->__('Step 1'),
                'description' => $this->__('General Settings')
            ],
            [
                'id' => 2,
                'title' => $this->__('Step 2'),
                'description' => $this->__('Selling Settings')
            ],
            [
                'id' => 3,
                'title' => $this->__('Step 3'),
                'description' => $this->__('Search Settings')
            ],
        ]);

        $this->setContainerData([
            'style' => 'margin-bottom: 30px;'
        ]);
    }

    //########################################
}