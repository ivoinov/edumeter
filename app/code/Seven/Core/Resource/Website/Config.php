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
 * @package    Core
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

    class Core_Resource_Website_Config extends Core_Resource_Db {
       
    	public function getTable() {
    		return "core_website_config";
    	}
    	
        protected function _getConfigSelect($website_id) {
            $area = $this->_getWebsiteArea($website_id);
            $select = $this->getConnection()
                    ->select($this->getTable() . ":default", array('path' => 'default.path'))
                    ->filter('default.scope', 'global')
                    ->joinl($this->getTable() . ":website", array('default.path = website.path', 'website.scope' => $website_id), false);
            
            if($area) {
                $select
                    ->joinl($this->getTable() . ":area", array('`default`.`path` = `area`.`path`', 'area.scope' => $area), false)
                    ->columns(array('value' => 'IF(`website`.`value` IS NOT NULL, `website`.`value`, IF(`area`.`value` IS NOT NULL, `area`.`value`, `default`.`value`))'));
            } else {
				$select->columns(array('value' => new Zend_Db_Expr('IF(website.value IS NOT NULL, website.value, default.value)')));
            }
            return $select;
        }
        
        protected function _getWebsiteArea($website_id) {
            return Seven::getModel('core/website')->load($website_id)->getArea();
        }
        
        public function getOptionsPair($scope, $inherit = true) {
            if($inherit)
                $select = $this->_getConfigSelect($scope);
            else
                $select = Seven::getDatabaseAdapter()
                    ->select($this->getTable() . ":default", array('path', 'value'))
                    ->filter('default.scope', array('is' => $scope));
           return $select->fetchPairs();
        }
        
        public function load($website_id) {                   
            $raw = $this->_getConfigSelect($website_id)->load();
            $config = array();
            foreach ($raw as $value) 
            	$this->_addOption($config, $value['path'], $value['value']);
            return $config;
        }
        
        protected function _addOption(&$config, $key, $value) {
            $keys = explode('/', $key);
            foreach ($keys as $key) {
                  if(!isset ($config[$key])) {
                      $config[$key] = array();
                  }
                  $config = &$config[$key];
            }
            return $config = $value;
        }

        public function save($options, $scope) {
            foreach($options as $key => $value) {
                Seven::getDatabaseAdapter()->place($this->getTable(), array('path' => $key, 'value' => $value, 'scope' => $scope));
            }
            return $this;
        }
        
        public function delete($options, $scope) {
            Seven::getDatabaseAdapter()
                    ->delete($this->getTable(), array('path' => array('in' => $options), 'scope' => $scope));
            return $this;
        }
        
}       
     