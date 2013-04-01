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

class Core_Model_Multiview_Entity extends Core_Model_Entity {
	
	protected $_view_id = false;
	
	public function _setView($view_id) {
		$views = $this->_getViews();
		if(! isset($views[$view_id]))
			$view_id = $this->_getDefaultView();
		if($this->_view_id != $view_id) {
			$this->_view_id = $view_id;
			// reload model if it's was loaded
			if($this->_getSearchField() && $this->_getSearchValue())
				$this->load($this->_getId());
		}
		return $this;
	}
	
	public function _getView() {
		if($this->_view_id === false)
			$this->_view_id = $this->_getDefaultView();
		return $this->_view_id;
	}
	
	public function _getViews() {
		static $views = null; 
		if($views === NULL) {
			$views = $this->_getResource()->getViews();
			if(! isset($views[$this->_getDefaultView()]))
				$views[$this->_getDefaultView()] = __("Default");
			ksort($views);
		}
		return $views;
	}
	
	public function _getDefaultView() {
		return 0;
	}
	
	public function _isDefault($key) {
		$current_view_data = $this->_getResource()->getViewData($this, $this->_getView());
		return empty($current_view_data[$key]);
	}
	
	public function _getMultiviewFields() {
		return $this->_getResource()->getMultiviewFields();
	}

}