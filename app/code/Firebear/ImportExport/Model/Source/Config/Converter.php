<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\Source\Config;

use Magento\Framework\Config\ConverterInterface;

class Converter implements ConverterInterface
{
    /**
     * Convert dom node tree to array
     *
     * @param \DOMDocument $source
     * @return array
     * @throws \InvalidArgumentException
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function convert($source)
    {
        $result = [];
        /** @var \DOMNode $templateNode */
        foreach ($source->documentElement->childNodes as $typeNode) {
            if ($typeNode->nodeType != XML_ELEMENT_NODE) {
                continue;
            }
            $typeName = $typeNode->attributes->getNamedItem('name')->nodeValue;
            $typeLabel = $typeNode->attributes->getNamedItem('label')->nodeValue;
            $typeModel = $typeNode->attributes->getNamedItem('modelInstance')->nodeValue;
            $sortOrder = $typeNode->attributes->getNamedItem('sortOrder')->nodeValue;
            if ($typeNode->attributes->getNamedItem('exists')) {
                $exists = $typeNode->attributes->getNamedItem('exists')->nodeValue;
                if ($exists && !class_exists($exists)) {
                    continue;
                }
            }
            $depends = '';
            if ($typeNode->attributes->getNamedItem('depends')) {
                $depends = $typeNode->attributes->getNamedItem('depends')->nodeValue;
            }
            $result[$typeName] = [
                'label' => $typeLabel,
                'model' => $typeModel,
                'sort_order' => $sortOrder,
                'depends' => $depends
            ];

            foreach ($typeNode->childNodes as $childNode) {
                if ($childNode->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }
                $result[$typeName]['fields'][$childNode->attributes->getNamedItem('name')->nodeValue] = [
                    'id' => $childNode->attributes->getNamedItem('id')->nodeValue,
                    'label' => $childNode->attributes->getNamedItem('label')->nodeValue,
                    'type' => $childNode->attributes->getNamedItem('type')->nodeValue,
                    'required' => ($childNode->attributes->getNamedItem('required'))
                        ? $childNode->attributes->getNamedItem('required')->nodeValue : false,
                    'validation' => ($childNode->attributes->getNamedItem('validation'))
                    ? $childNode->attributes->getNamedItem('validation')->nodeValue : null,
                    'notice' => ($childNode->attributes->getNamedItem('notice'))
                        ? $childNode->attributes->getNamedItem('notice')->nodeValue : '',
                    'componentType' => ($childNode->attributes->getNamedItem('componentType'))
                        ? $childNode->attributes->getNamedItem('componentType')->nodeValue : '',
                    'component' => ($childNode->attributes->getNamedItem('component'))
                        ? $childNode->attributes->getNamedItem('component')->nodeValue : '',
                    'template' => ($childNode->attributes->getNamedItem('template'))
                        ? $childNode->attributes->getNamedItem('template')->nodeValue : '',
                    'url' => ($childNode->attributes->getNamedItem('url'))
                        ? $childNode->attributes->getNamedItem('url')->nodeValue : '',
                ];
            }
        }

        return $result;
    }
}
