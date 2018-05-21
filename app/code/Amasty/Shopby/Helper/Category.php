<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Helper;

use Amasty\Shopby\Model\Source\RenderCategoriesLevel;
use Magento\Store\Model\ScopeInterface;
use Amasty\Shopby\Model\Category\Attribute\Frontend\Image as ImageModel;

/**
 * Class Category
 * @package Amasty\Shopby\Helper
 */
class Category extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ATTRIBUTE_CODE = 'category_ids';
    const STORE_CODE = 'store_id';
    const CHILDREN_CATEGORIES_SETTING_PATH = 'amshopby/children_categories/';
    const DEFAULT_CATEGORY_FILTER_IMAGE_SIZE = 20;

    /**
     * @var \Amasty\Shopby\Api\Data\FilterSettingInterface
     */
    protected $setting;

    /**
     * @var \Amasty\Shopby\Model\Category\Manager\Proxy
     */
    protected $categoryManager;

    /**
     * @var \Magento\Catalog\Model\Layer
     */
    protected $layer;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $startCategory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var array
     */
    protected $categoryImageById;

    /**
     * @var ImageModel
     */
    protected $image;

    /**
     * Category constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param FilterSetting $settingHelper
     * @param \Amasty\Shopby\Model\Category\Manager\Proxy $categoryManager
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Amasty\Shopby\Helper\FilterSetting $settingHelper,
        \Amasty\Shopby\Model\Category\Manager\Proxy $categoryManager,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        ImageModel $image
    ) {
        parent::__construct($context);
        $this->setting = $settingHelper->getSettingByAttributeCode(self::ATTRIBUTE_CODE);
        $this->categoryManager = $categoryManager;
        $this->layer = $layerResolver->get();
        $this->categoryRepository = $categoryRepository;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getStartCategory()
    {
        if ($this->startCategory === null) {
            $this->init();
        }

        return $this->startCategory;
    }

    /**
     * @return bool
     */
    public function isCategoryFilterExtended()
    {
        return $this->setting->getCategoryTreeDepth() > 1;
    }

    /**
     * Category filter initialization
     *
     * @return $this
     */
    protected function init()
    {
        if ($this->setting->getRenderCategoriesLevel() == RenderCategoriesLevel::ROOT_CATEGORY) {
            $category = $this->categoryRepository->get(
                $this->categoryManager->getRootCategoryId(),
                $this->categoryManager->getCurrentStoreId()
            );
        } elseif ($this->setting->getRenderCategoriesLevel() == RenderCategoriesLevel::CURRENT_CATEGORY_LEVEL) {
            if ($this->layer->getCurrentCategory()->getId() == $this->categoryManager->getRootCategoryId()) {
                $category = $this->layer->getCurrentCategory();
            } else {
                $categoryId = $this->layer->getCurrentCategory()->getParentId();
                $category = $this->categoryRepository->get($categoryId, $this->categoryManager->getCurrentStoreId());
            }
        } else { //  RenderCategoriesLevel::CURRENT_CATEGORY_CHILDREN
            $category = $this->layer->getCurrentCategory();
        }
        $this->startCategory = $category;

        return $this;
    }

    /**
     * @param $categoryId
     * @param string $imageType
     * @return string|null
     */
    protected function getCategoryImage($categoryId, $imageType = 'thumbnail')
    {
        if (empty($this->categoryImageById[$imageType])) {
            $collection = $this->categoryCollectionFactory->create();
            $collection->addAttributeToSelect($imageType);
            foreach ($collection as $item) {
                $this->categoryImageById[$imageType][$item->getId()] = $item->getData($imageType);
            }
        }
        return isset($this->categoryImageById[$imageType][$categoryId])
            ? $this->categoryImageById[$imageType][$categoryId] : null;
    }

    /**
     * @param int $categoryId
     * @param string $imageType
     * @return string
     */
    public function getCategoryImageUrl($categoryId, $imageType = 'thumbnail')
    {
        return $this->getImageUrl(
            $this->getCategoryImage($categoryId, $imageType),
            true,
            $this->getCategoryFilterImageSize()
        );
    }

    /**
     * @param $imageName
     * @param bool $withPlaceholder
     * @param null $width
     * @param null $height
     * @return bool|null|string
     */
    public function getImageUrl($imageName, $withPlaceholder = false, $width = null, $height = null)
    {
        return $this->image->getImageUrl($imageName, $withPlaceholder, $width, $height);
    }

    /**
     * @return int
     */
    public function getCategoryFilterImageSize()
    {
        return self::DEFAULT_CATEGORY_FILTER_IMAGE_SIZE;
    }

    /**
     * @param string $path
     * @param bool $flag
     * @return bool|mixed
     */
    private function getChildrenCategoriesSetting($path, $flag = false)
    {
        if ($flag) {
            return $this->scopeConfig->isSetFlag(
                self::CHILDREN_CATEGORIES_SETTING_PATH . $path,
                ScopeInterface::SCOPE_STORE
            );
        }
        return $this->scopeConfig->getValue(
            self::CHILDREN_CATEGORIES_SETTING_PATH . $path,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isChildrenCategoriesBlockEnabled()
    {
        return $this->getChildrenCategoriesSetting('enabled', true);
    }

    /**
     * @return bool
     */
    public function isChildrenCategoriesSliderEnabled()
    {
        return $this->getChildrenCategoriesSetting('slider_enabled', true);
    }

    /**
     * @return int
     */
    public function getChildrenCategoriesBlockImageSize()
    {
        return $this->getChildrenCategoriesSetting('image_size');
    }

    /**
     * @return int
     */
    public function getChildrenCategoriesItemsCountPerSlide()
    {
        return $this->getChildrenCategoriesSetting('items_per_slide');
    }

    /**
     * @return bool
     */
    public function showChildrenCategoriesImageLabels()
    {
        return $this->getChildrenCategoriesSetting('show_labels', true);
    }
}
