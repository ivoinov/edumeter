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

class Core_Block_Widget_Menu_Item extends Core_Block_Template {

	protected $_children = array();

	public function __construct($data = array()) {
	    if(!isset($data['template']))
		$data['template'] = 'widgets/menu/item.phtml';
	    parent::__construct($data);
	}

	public function addChild($id, $child) {
		$this->_children[$id] = $child;
		return $this;
	}

	public function getChildren() {
		return $this->_children;
	}

	public function getOrder() {
		if(!parent::hasOrder())
		return 1;
		return parent::getOrder();
	}

	public function getSortedChildren() {
		$children = $this->getChildren();
		usort($children, array($this, '_cmpChild'));
		return $children;
	}

	public function getHref() {
		if($this->hasHref())
			return parent::getHref();
		if($this->hasUrl())
			return seven_url($this->getUrl());
		return null;
	} 
	
	public function _cmpChild($a, $b) {
		if($a->getOrder() == $b->getOrder()) return 0;
		if($a->getOrder() > $b->getOrder()) return 1;
		return -1;
	}
	
	public function getUrl($url = null, $args = array()) {
	    return Seven_Object::getUrl();
	}

	public function isAllowed() {
		if($this->getChildren()) {
			foreach($this->getChildren() as $child) {
				if($child->isAllowed())
					return true;
			}
			return false;
		} else {
			$permissions = new Seven_Object(array('allowed' => null, 'route' => $this->getHref()));
			Seven::app()->event('route_access', $permissions);
			return $permissions->getAllowed() || $permissions->getAllowed() === null;
		}
	}
	
}