<?php

use Mage;

trait HandleAttributes
{
    /**
     * @param Mage_Catalog_Model_Product $simple
     * @param Mage_Catalog_Model_Product $configurable
     * @param array                      $attributeIds
     * @param array                      $data
     * @return mixed
     */
    public function associateWithConfigurable(Mage_Catalog_Model_Product $simple, Mage_Catalog_Model_Product $configurable, $attributeIds = [92], $data = [])
    {
        $configurable->getTypeInstance()->setUsedProductAttributeIds($attributeIds);
        $configurableAttributesData = $configurable->getTypeInstance()->getConfigurableAttributesAsArray();
        $configurable->setCanSaveConfigurableAttributes(true);
        $configurable->setConfigurableAttributesData($configurableAttributesData);

        if (!count($data)) {
            $data[$simple->getId()] = [
                '0' => [
                    'label' => 'Green', //attribute label
                    'attribute_id' => '92', //attribute ID of attribute 'color'
                    'value_index' => '3', //value of 'Green' index of the attribute 'color'
                    'is_percent' => '0', //fixed/percent price for this option
                    'pricing_value' => '21' //value for the pricing
                ]
            ];
        }
        $configurable->setConfigurableProductsData($data);
        return $configurable->save();
    }

    public function addAttributeToAttributeSet($attributeId, $attributeSetId, $attributeGroupName = 'General')
    {
        $model = Mage::getModel('eav/entity_setup','core_setup');
        $attributeGroupId = $model->getAttributeGroup('catalog_product', $attributeSetId, $attributeGroupName);
        $model->addAttributeToSet('catalog_product', $attributeSetId, $attributeGroupId, $attributeId);
    }

    public function addAttributeOption($attributeCode, $attributeValue)
    {
        $attributeModel = Mage::getModel('eav/entity_attribute');
        $attributeOptionsModel= Mage::getModel('eav/entity_attribute_source_table') ;

        $attributeId = $attributeModel->getIdByCode('catalog_product', $attributeCode);
        $attribute = $attributeModel->load($attributeId);
        $attributeOptionsModel->setAttribute($attribute);
        $options = $attributeOptionsModel->getAllOptions(false);

        foreach($options as $option) {
            // checking if already exists
            if ($option['label'] == $attributeValue) {
                $optionId = $option['value'];
                return $optionId;
            }
        }

        $value['option'] = array($attributeValue, $attributeValue);
        $result = array('value' => $value);
        $attribute->setData('option', $result);
        return $attribute->save();
    }

    public function createAttribute($code, $label, $attributeType, $productType)
    {
        $attributeData = array(
            'attribute_code' => $code,
            'is_global' => '1',
            'frontend_input' => $attributeType,
            'default_value_text' => '',
            'default_value_yesno' => '0',
            'default_value_date' => '',
            'default_value_textarea' => '',
            'is_unique' => '0',
            'is_required' => '0',
            'apply_to' => array($productType),
            'is_configurable' => '0',
            'is_searchable' => '0',
            'is_visible_in_advanced_search' => '1',
            'is_comparable' => '1',
            'is_used_for_price_rules' => '0',
            'is_wysiwyg_enabled' => '0',
            'is_html_allowed_on_front' => '0',
            'is_visible_on_front' => '1',
            'used_in_product_listing' => '1',
            'used_for_sort_by' => '1',
            'frontend_label' => array($label)
        );


        $model = Mage::getModel('catalog/resource_eav_attribute');

        if (!isset($attributeData['is_configurable'])) {
            $attributeData['is_configurable'] = 0;
        }
        if (!isset($attributeData['is_filterable'])) {
            $attributeData['is_filterable'] = 0;
        }
        if (!isset($attributeData['is_filterable_in_search'])) {
            $attributeData['is_filterable_in_search'] = 0;
        }

        if (is_null($model->getIsUserDefined()) || $model->getIsUserDefined() != 0) {
            $attributeData['backend_type'] = $model->getBackendTypeByInput($attributeData['frontend_input']);
        }

        $defaultValueField = $model->getDefaultValueByInput($attributeData['frontend_input']);
        if ($defaultValueField) {
            $attributeData['default_value'] = '';
        }


        $model->addData($attributeData);

        $model->setEntityTypeId(Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId());
        $model->setIsUserDefined(1);


        try {
            $model->save();
        } catch (Exception $e) {
//            echo "\033[31m".$e->getMessage() ."\033[0m" . PHP_EOL . PHP_EOL;
        }
    }

}