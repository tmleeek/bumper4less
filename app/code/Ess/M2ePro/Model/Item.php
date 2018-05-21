<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Model;

class Item extends \Ess\M2ePro\Model\AbstractModel
{
    protected $resourceConnection;

    //########################################

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Ess\M2ePro\Helper\Factory $helperFactory,
        \Ess\M2ePro\Model\Factory $modelFactory
    )
    {
        $this->resourceConnection = $resourceConnection;
        parent::__construct($helperFactory, $modelFactory);
    }

    //########################################

    public function removeDeletedProduct($product, $component = null)
    {
        $productId = $product instanceof \Magento\Catalog\Model\Product
                        ? (int)$product->getId() : (int)$product;

        $connection = $this->resourceConnection->getConnection();
        $existTables = $this->getHelper('Magento')->getMySqlTables();

        if (is_null($component)) {
            $components = $this->getHelper('Component')->getComponents();
        } else {
            $components = array($component);
        }

        foreach ($components as $component) {
            $itemTable = $this->resourceConnection->getTableName("m2epro_{$component}_item");
            if (!in_array($itemTable, $existTables)) {
                continue;
            }
            $connection->delete($itemTable, array('product_id = ?' => $productId));
        }
    }

    //########################################
}