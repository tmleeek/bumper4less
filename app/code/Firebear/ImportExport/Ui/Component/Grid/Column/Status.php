<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Ui\Component\Grid\Column;

use Firebear\ImportExport\Model\Job;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Ui\Component\Listing\Columns\Column;

class Status extends Column
{
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$fieldName])) {
                    if (Job::STATUS_ENABLED == $item[$fieldName]) {
                        $item[$fieldName] = '<span class="grid-severity-major"><span>'
                            . $item[$fieldName] . '</span></span>';
                    } else {
                        $item[$fieldName] = '<span class="grid-severity-notice"><span>'
                            . $item[$fieldName] . '</span></span>';
                    }
                    $item[$fieldName] = [
                        'class' => 'asdasdasd',
                        'label' => 'Enabled',
                        'value' => 1
                    ];
                }
            }
        }

        return $dataSource;
    }
}
