<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Blog
 */

namespace Amasty\Blog\Block\Content;

class Post extends AbstractBlock implements \Magento\Framework\DataObject\IdentityInterface
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Amasty_Blog::post.phtml');
    }

    public function getPost()
    {
        if (!$this->registry->registry('current_post')){
            if ($postId = $this->getRequest()->getParam('id')){
                if (!$this->storeManagerInterface->isSingleStoreMode()){
                    $this->postsModel->setStore($this->storeManagerInterface->getStore()->getId());
                }
                $this->postsModel->load($postId);
                $this->registry->register('current_post', $this->postsModel, true);
            } else {
                new \Exception(__("Unknown post id."));
            }
        }
        return $this->registry->registry('current_post');
    }

    protected function _prepareLayout()
    {
        $this->_title = $this->getPost()->getTitle();
        parent::_prepareLayout();
        return $this;
    }

    public function getMetaTitle()
    {
        return $this->getPost()->getMetaTitle() ? $this->settingsHelper->getPrefixTitle($this->getPost()->getMetaTitle()) : $this->settingsHelper->getPrefixTitle($this->getPost()->getTitle());
    }

    public function getDescription()
    {
        return $this->getPost()->getMetaDescription();
    }

    public function getKeywords()
    {
        return $this->getPost()->getMetaTags();
    }

    protected function _prepareBreadcrumbs()
    {
        parent::_prepareBreadcrumbs();

        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs){
            $breadcrumbs->addCrumb('blog', array(
                'label' => $this->settingsHelper->getBreadcrumb(),
                'title' => $this->settingsHelper->getBreadcrumb(),
                'link' => $this->urlHelper->getUrl(),
            ));

            $breadcrumbs->addCrumb('post', array(
                'label' => $this->getTitle(),
                'title' => $this->getTitle(),
            ));
        }
    }

    public function getCommentsActionHtml()
    {
        return $this->getChildHtml('amblog_comments_action');
    }

    public function getCommentsHtml()
    {
        return $this->getChildHtml('amblog_comments_list');
    }

    public function getSocialHtml()
    {
        return $this->getChildHtml('amblog_social');
    }

    public function getColorClass()
    {
        return $this->settingsHelper->getIconColorClass();
    }

    public function getShowPrintLink()
    {
        return $this->settingsHelper->getShowPrintLink();
    }

    public function hasThumbnailUrl()
    {
        return !!$this->getPost()->getThumbnailUrl();
    }

    public function getThumbnailUrl()
    {
        $url = $this->getPost()->getThumbnailUrl();
        $url = $this->filter->filter($url);

        return $url;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [\Amasty\Blog\Model\Posts::CACHE_TAG . '_' . $this->getPost()->getId()];
    }
}
