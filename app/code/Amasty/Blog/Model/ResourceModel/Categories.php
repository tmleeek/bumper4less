<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */

namespace Amasty\Blog\Model\ResourceModel;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Exception\AlreadyExistsException;

class Categories extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->storeManager = $storeManager;
    }

    public function _construct()
    {
        $this->_init('amasty_blog_categories', 'category_id');
    }

    /**
     * Perform actions after object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
        $connection = $this->getConnection();
        /**
         * save stores
         */
        $stores = $object->getStoreIds();
        if (!empty($stores)) {
            $condition = ['category_id = ?' => $object->getCategoryId()];
            $connection->delete($this->getTable('amasty_blog_categories_store'), $condition);

            $insertedStoreIds = [];
            foreach ($stores as $storeId) {
                if (in_array($storeId, $insertedStoreIds)) {
                    continue;
                }

                $insertedStoreIds[] = $storeId;
                $storeInsert = ['store_id' => $storeId, 'category_id' => $object->getId()];
                $connection->insert($this->getTable('amasty_blog_categories_store'), $storeInsert);
            }
        }
    }

    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$this->_validateUrlKey($object) && ($object->getStatus() != \Amasty\Blog\Model\Categories::STATUS_DISABLED)){
            $object->setStatus(\Amasty\Blog\Model\Categories::STATUS_DISABLED);

            throw new AlreadyExistsException(__("Category '%1' can be disabled only. Some category has same Url Key for the same Store View.", $object->getTitle()));
        }

        return parent::_beforeSave($object); // TODO: Change the autogenerated stub
    }

    protected function _validateUrlKey($object)
    {

        $store = $object->getStores();
        if (!is_array($store)){
            $store = array($store);
        }

        $connection = $this->getConnection();
        $bind = ['url_key' => $object->getUrlKey()];

        $select = $connection->select()->from(
            ['main_table' => $this->getMainTable()],
            [$this->getIdFieldName()]
        )->joinLeft(
            ['store' => $this->getTable('amasty_blog_categories_store')],
            'main_table.category_id = store.category_id',
            ['store.store_id']
        )->where(
            'main_table.url_key = :url_key'
        );

        if ($object->getId()) {
            $bind['category_id'] = (int)$object->getId();
            $select->where('store.category_id != :category_id');
        }

        $bind['store_id'] = implode(', ', $store);
        $select->where('store.store_id IN (:store_id)');

        $result = $connection->fetchOne($select, $bind);
        if ($result !== false) {
            throw new AlreadyExistsException(
                __('A Category with the same url key already exists.')
            );
        }
        return true;
    }

    /**
     * Perform actions after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('amasty_blog_categories_store'),
            ['store_id']
        )->where(
            'category_id = :category_id'
        );

        $stores = $connection->fetchCol($select, [':category_id' => $object->getId()]);
        if (empty($stores) && $this->storeManager->hasSingleStore()) {
            $object->setStoreIds([$this->storeManager->getStore(true)->getId()]);
        } else {
            $object->setStoreIds($stores);
        }
        return $this;
    }

    public function getStores($id)
    {
        $select = $this->getConnection()->select()
            ->from(
                [$this->getTable('amasty_blog_categories_store')],
                ['category_id', 'store_id']
            )
            ->where('category_id = :category_id');

        return $this->getConnection()->fetchAll($select, [':category_id' => $id]);
    }

}
