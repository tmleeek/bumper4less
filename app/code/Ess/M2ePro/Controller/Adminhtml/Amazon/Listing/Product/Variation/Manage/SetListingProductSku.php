<?php

namespace Ess\M2ePro\Controller\Adminhtml\Amazon\Listing\Product\Variation\Manage;

use Ess\M2ePro\Controller\Adminhtml\Amazon\Main;

class SetListingProductSku extends Main
{
    public function execute()
    {
        $listingProductId = $this->getRequest()->getParam('product_id');
        $sku = $this->getRequest()->getParam('sku');
        $msg = '';

        if (empty($listingProductId) || is_null($sku)) {
            $this->setAjaxContent('You should provide correct parameters.', false);
            return $this->getResult();
        }

        $listingProduct = $this->amazonFactory->getObjectLoaded('Listing\Product', $listingProductId);

        if ($this->isExistInM2eProListings($listingProduct, $sku)) {
            $msg = $this->__('This SKU is already being used in M2E Pro Listing.');
        } else if ($this->isExistInOtherListings($listingProduct, $sku)) {
            $msg = $this->__('This SKU is already being used in M2E Pro 3rd Party Listing.');
        } else {

            $skuInfo = $this->getSkuInfo($listingProduct, $sku);

            if (!$skuInfo) {
                $msg = $this->__('This SKU is not found in your Amazon Inventory.');
            } else if ($skuInfo['info']['type'] != 'parent') {
                $msg = $this->__('This SKU is used not for Parent Product in your Amazon Inventory.');
            } else if (!empty($skuInfo['info']['bad_parent'])) {
                $msg = $this->__(
                    'Working with found Amazon Product is impossible because of the
                    limited access due to Amazon API restriction'
                );
            } else if ($skuInfo['asin'] != $listingProduct->getChildObject()->getGeneralId()) {
                $msg = $this->__(
                    'The ASIN/ISBN of the Product with this SKU in your Amazon Inventory is different
                     from the ASIN/ISBN for which you want to set you are creator.'
                );
            }
        }

        if (!empty($msg)) {
            $this->setJsonContent([
                'success' => false,
                'msg' => $msg
            ]);

            return $this->getResult();
        }

        $this->getHelper('Data\Session')->setValue('listing_product_setting_owner_sku_' . $listingProductId, $sku);

        $this->setJsonContent(['success' => true]);

        return $this->getResult();
    }

    private function isExistInM2eProListings($listingProduct, $sku)
    {
        $listingTable = $this->activeRecordFactory->getObject('Listing')->getResource()->getMainTable();

        $collection = $this->amazonFactory->getObject('Listing\Product')->getCollection();
        $collection->getSelect()->join(
            array('l'=>$listingTable),
            '`main_table`.`listing_id` = `l`.`id`',
            array()
        );

        $collection->addFieldToFilter('sku',$sku);
        $collection->addFieldToFilter('account_id',$listingProduct->getAccount()->getId());

        return $collection->getSize() > 0;
    }

    private function isExistInOtherListings($listingProduct, $sku)
    {
        $collection = $this->amazonFactory->getObject('Listing\Other')->getCollection();

        $collection->addFieldToFilter('sku',$sku);
        $collection->addFieldToFilter('account_id',$listingProduct->getAccount()->getId());

        return $collection->getSize() > 0;
    }

    private function getSkuInfo($listingProduct, $sku)
    {
        try {

            /** @var $dispatcherObject \Ess\M2ePro\Model\Amazon\Connector\Dispatcher */
            $dispatcherObject = $this->modelFactory->getObject('Amazon\Connector\Dispatcher');
            $connectorObj = $dispatcherObject->getVirtualConnector('product','search','asinBySkus',
                array('include_info'  => true,
                    'only_realtime' => true,
                    'items'         => array($sku)),
                'items', $listingProduct->getAccount()->getId());

            $dispatcherObject->process($connectorObj);
            $response = $connectorObj->getResponseData();

        } catch (\Exception $exception) {
            $this->getHelper('Module\Exception')->process($exception);

            return false;
        }

        return $response[$sku];
    }
}