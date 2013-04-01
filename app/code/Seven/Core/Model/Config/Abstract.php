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

abstract class Core_Model_Config_Abstract {
	
	protected $_area_config = array();
	protected $_area_nodes = array();
	protected $_preloaded = false;
	
	protected function _xml2array($xml_object) {
		$xml_array = array();
		$to_translate = ($xml_object->attributes()->translate) ? array_map('trim', explode(',', $xml_object->attributes()->translate)) : array();
		foreach($xml_object->children() as $index => $node) {
			$xml_array[$index] = (is_object($node) && $node->count()) ? 
										$this->_xml2array($node) : 
										(!in_array($index, $to_translate) ? (string) $node : $this->__((string) $node));
		}
		return $xml_array;
	}

	protected function __($string) {
		return new Seven_Smartstring($string, array('__'/*, function($string) { debug()->log( $string . '=>' . __($string), E_NOTICE ); return $string; }*/));		
	}
	
	public function preloadConfig() {
		if($this->_preloaded)
			return; // don't do recursion
		$this->_preloaded = true;
		return $this->_preloadConfig();
	}
	
	abstract protected function _preloadConfig();

	protected function _loadConfigPath($path) {
		$config = array();
		$files = glob(rtrim($path, DS) . DS . "*.xml");
		foreach($files as $file) {
			debug()->open("Load config file " . basename($file), array('file' => $file));
			$areas = simplexml_load_file($file);
			foreach($areas as $area => $node)
				$this->_addAreaNode($area, $node);
			debug()->close();
		}
		return $config;
	}
	
	protected function _addAreaNode($area, $node) {
		$this->reset($area);
		if(! isset($this->_area_nodes[$area]))
			$this->_area_nodes[$area] = array();
		$this->_area_nodes[$area][] = $node;
	}
	
	public function reset($area = null) {
		if($area === null) { 
			$this->area_config = array();
		} else if(isset($this->area_config[$area])) { 
			unset($this->area_config[$area]);
		}
		return $this;
	}
	
	protected function _getAreaConfig($area) {
		if(empty($this->area_config[$area])) {
			$this->preloadConfig();
			$this->area_config[$area] = array();
			if(isset($this->_area_nodes[$area])) {
				foreach($this->_area_nodes[$area] as $node) {
					$this->area_config[$area] = array_merge_recursive_replace($this->area_config[$area], $this->_xml2array($node));
				}
				if($extends = $this->_getAreaExtends($area, $this->area_config[$area]))
					foreach($extends as $_area => $_ignore) {
						if($_ignore) continue;
						$this->area_config[$area] = array_merge_recursive_replace($this->_getAreaConfig($_area), $this->area_config[$area]);
					}
			}
		}
		return $this->area_config[$area];
	}
	
	protected function _getAreaExtends($area, $collected) {
		return isset($collected['extends']) ? (array)$collected['extends'] : array();
	} 
	
	public function getOption($key, $area = null) {
		if($area === null) 	
			$area = Seven::app()->getWebsite() ? Seven::app()->getWebsite()->getArea() : 'global';

		$config = $this->_getAreaConfig($area);

		if(empty($key))
			return $config;

		$key = explode("/", $key);
		foreach($key as $k => $value) {
			if(!isset($config[$value]))
				return NULL;
			if(is_array($config[$value]))
				$config = &$config[$value];
			else
				$config = $config[$value];
		}
		return $config;
	}

}