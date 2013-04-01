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

class Core_Resource_Package_Collection extends Core_Resource_Entity_Collection {

	protected $_alias = "core/package";

	public function getSelect() {
		if($this->_select === NULL) {
			parent::getSelect()->order(array('index' => 'ASC'));
		}
		return parent::getSelect();
	}

	public function getActive() {
//		$system_packages = array_filter(array_keys((array)Seven::getConfig('required_packages', 'system')));
		$disabled_packages = array_filter(array_keys((array)Seven::getConfig('disabled_packages', 'system')));

		foreach($disabled_packages as $package_id) {
			if(strpos($package_id, 'Seven_') === 0)
				$disabled_packages[] = substr($package_id, 6);
		}

/*		foreach($system_packages as $package_id) {
			if(strpos($package_id, 'Seven_') === 0)
				$system_packages[] = substr($package_id, 6);
		}*/

		$this->getAvailabel();
		$this->filter('active', 1);
		if($disabled_packages)
			$this->filter('id', array('nin' => $disabled_packages));
//		if($system_packages)
//			$this->filter('id', array('in' => $system_packages));
		return $this;
	}

	public function getAvailabel() {
		return $this->filter('availabel', 1);
	}

}
