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

class Core_Model_Package_Config extends Core_Model_Config_Abstract {

	protected $_package = null;
	
	public function setPackage($package) {
		$this->_package = $package;
		return $this;
	}
	
	public function getPackage() {
		return $this->_package;
	}
	
	protected function  _preloadConfig(){
		$this->_loadConfigPath($this->getPackage()->getBasePath() . DS . "etc" . DS);
	}
	
	protected function _getAreaExtends($area, $collected) {
		return Seven::getConfig('extends', $area);
	}
	
	public function getOption($key, $area = null) {
		return parent::getOption($key, $area ? $area : "global");
	}
	
}