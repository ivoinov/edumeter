<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * @category   Seven
 * @package    Backoffice
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

class Backoffice_Model_Form_System_Config extends Core_Model_Form_Xml {
    
    public function setScope($scope) {
        parent::setScope($scope);
        $this->_fields = null;
        $this->initXml("admin/system_config");
        return $this;
    }
    
    public function getArea() {
        if(is_numeric($this->getScope()))
            return Seven::getModel('core/website')->load($this->getScope())->getArea();
        return $this->getScope();
    }

    public function addField($id, $data = array()) {
        if(!isset($data['area'])) 
            $data['area'] = 'global';
        if($this->getScope() == 'global' || ($data['area'] == 'global' && $this->getScope() != 'system') || $data['area'] == $this->getArea())
            parent::addField($id, $data);
        return $this;
    }    
    
    protected function _save() {
        $reset = $options = array();
        foreach($this->getFields() as $name => $field) {
            if(!$field->hasConfigKey())
                $field->setConfigKey(str_replace('_', '/', $name));

            if($field->getOptional() && !$field->isSubmit()) {
                $reset[$field->getConfigKey()] = $field->getConfigKey();
            } else {
                $options[$field->getConfigKey()] = $field->getValue();
            }
        }
        // update database
        Seven::getModel('core/website_config')
            ->setOption($options, null, $this->getScope())
            ->resetOption($reset, $this->getScope());
    }

    protected function _getDefaults() {
        if($this->getScope() == 'global') 
            return new Seven_Object();
        $scope = Seven::getModel('core/website')->load($this->getScope())->getArea();
        if(!$scope) 
            $scope = 'global';
        return new Seven_Object(Seven::getModel('core/website_config')->getOptionsPair($scope));
    }
    
    protected function _load($source = array()) {
        $source = new Seven_Object(Seven::getModel('core/website_config')->getOptionsPair($this->getScope(), false));
        $defaults = $this->_getDefaults();
        foreach($this->getFields() as $name => $field) {
            // get real config key
            if(!$field->hasConfigKey()) 
                $field->setConfigKey(str_replace('_', '/', $name));
            // check if option exists in current scope
            if($has_option = $source->hasData($field->getConfigKey()))
                $field->setValue($source->getData($field->getConfigKey()));
            // in case of available default value setup field to allow it
            if($this->getScope() != 'global')
                $field->setUseDefault(!$has_option)
                    ->setOptional(true);
            // set default value as current
            if(!$has_option && $defaults->hasData($field->getConfigKey()))
                $field->setValue($defaults->getData($field->getConfigKey()));
        }
    }    
 }
 
