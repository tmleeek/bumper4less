<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Controller\Adminhtml\Option;

use Amasty\Shopby\Model\Cache\Type;
use Magento\Backend\App\Action;
use Magento\Framework\App\Cache\TypeListInterface;

class Save extends \Amasty\Shopby\Controller\Adminhtml\Option
{
    /**
     * @var  TypeListInterface
     */
    private $cacheTypeList;

    public function __construct(Action\Context $context, TypeListInterface $typeList)
    {
        parent::__construct($context);
        $this->cacheTypeList = $typeList;
    }

    public function execute()
    {
        $filterCode = $this->getRequest()->getParam('filter_code');
        $optionId = $this->getRequest()->getParam('option_id');
        $storeId = $this->getRequest()->getParam('store', 0);
        /** @var \Amasty\Shopby\Model\OptionSetting $model */
        if ($data = $this->getRequest()->getPostValue()) {
            try {

                /** @var \Amasty\Shopby\Model\OptionSetting $model */
                $model = $this->_objectManager->create(\Amasty\Shopby\Model\OptionSetting::class);
                $inputFilter = new \Zend_Filter_Input(
                    [],
                    [],
                    $data
                );
                $data = $inputFilter->getUnescaped();

                $model->saveData($filterCode, $optionId, $storeId, $data);
                
                $session = $this->_session;
                $session->setPageData($model->getData());

                $this->cacheTypeList->invalidate(Type::TYPE_IDENTIFIER);
                $this->messageManager->addSuccess(__('You saved the item.'));
                $session->setPageData(false);
                //$this->_redirect('*/*/settings', ['option_id'=>(int)$optionId, 'filter_code'=>$filterCode]);
                if ($this->getRequest()->getParam('back')) {
                    return $this->_forward(
                        'edit',
                        null,
                        null,
                        ['filter_code' => $filterCode, 'option_id' => $optionId, 'store' => $storeId]
                    );
                }
                $this->_redirectRefer();
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirectRefer();
                return;
                //return $this->_redirect('*/*/settings', ['option_id'=>(int)$optionId, 'filter_code'=>$filterCode]);
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->messageManager->addError(
                    __('Something went wrong while saving the item data. Please review the error log.')
                );
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                $this->_session->setPageData($data);
                $this->_redirectRefer();
                return;
            }
        }
        $this->_redirectRefer();
        return;
    }

    protected function _redirectRefer()
    {
        $this->_forward('settings');
    }
}
