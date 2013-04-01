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
    
    class Core_Model_Website_Config extends Core_Model_Abstract {
      
        public function getOptions($website_id = NULL) {
            $website_id = $this->_getWebsiteId($website_id);
            $options = parent::getOptions();

            if(empty($options[$website_id])) {
                debug()->open("Loading {$website_id} website configuration");
                $options[$website_id] = Seven::getResource('core/website_config')->load($website_id);
                debug()->close();
                parent::setOptions($options);
            }
            return $options[$website_id];
        }
        
        public function getOptionsPair($website_id = NULL, $inherit = true) {
            $website_id = $this->_getWebsiteId($website_id);
            return Seven::getResource('core/website_config')->getOptionsPair($website_id, $inherit);
        }
                
        public function getAreaOptionsPair($area) {
            return Seven::getResource('core/website_config')->getAreaOptionsPair($area);
        }
        
        public function getOption($key, $website_id = NULL) {
            $options = $this->getOptions($website_id);
            $keys = explode("/", $key);
            foreach($keys as $k=>$value) {
                if(!isset($options[$value])) return NULL;
                $options = &$options[$value];
            }
            return $options;
            
        }

        protected function _getWebsiteId($website_id) {
            if($website_id === NULL)
                $website_id = Seven::app()->getWebsite()->_getId();
            return $website_id;
        }
        
        public function setOption($option, $value = null, $scope = null) {
            $scope = $this->_getWebsiteId($scope);
            if(!is_array($option))
                $option = array($option => $value); 
            Seven::getResource('core/website_config')->save($option, $scope);
            if(parent::getOptions())
                parent::addData('options',  array_merge( parent::getOptions(), array($scope => $option) ));
            return $this;
        }
        
        public function resetOption($option, $scope = null) {
            $scope = $this->_getWebsiteId($scope);
            if(!is_array($option))
                $option = array($option => $option); 
            Seven::getResource('core/website_config')->delete($option, $scope);
            parent::unsData('options'); // reset preloaded configuration
            return $this;
        }   
        
        public function getScopesArray(){
            $areas = array();
    	    $options = array();
            $options['global'] = __("All settings");
    	    foreach(Seven::getModel('core/website')->getCollection()->load() as $item)
    		$areas[$item->getArea()][$item->_getId()] = "&nbsp; &nbsp; &nbsp; &nbsp; " . $item->getName();
            foreach($areas as $area => $sites) {
                $options[$area] = "&nbsp; &nbsp;" . Seven::getConfig('areas/' . $area . '/name');
                if(count($sites) > 1) 
                    foreach($sites as $id => $name)
                        $options[$id] = $name;
            }
            return $options;
        }
        
        
    } 
