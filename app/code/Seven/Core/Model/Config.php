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

class Core_Model_Config extends Core_Model_Config_Abstract {
	
	protected $_preloaded_base		= false;

	protected function _preloadBaseConfig() {
		if($this->_preloaded_base)
			return; // don't do recursion
		$this->_preloaded_base = true;
		$this->_loadConfigPath(BP . DS . 'app' . DS . 'etc');
	}
	
	protected function _preloadConfig() {
		$packages = Seven::app()->getLoadedPackages();
		foreach($packages as $package) {
			$this->_loadConfigPath($package->getBasePath() . DS . "etc" . DS);
		}
	}
	
	protected function _getAreaConfig($area) {
		if(empty($this->area_config[$area])) {
			$this->_preloadBaseConfig();
			if($area != 'system') {
				$cached = Seven::cache()->load('xml_config_area_' . $area);
				if(!empty($cached))
					return $this->area_config[$area] = (array)$cached;
				$this->preloadConfig();
			} 
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
				if($area != 'system')
					Seven::cache()->save($this->area_config[$area], 'xml_config_area_' . $area, array('config_xml'));
			}
		}
		return $this->area_config[$area];
	}

}