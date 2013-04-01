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

class Core_Block_Widget_Sitemap extends Core_Block_Widget_Menu {

	public function setSitemapPath($path, $deep = 0) {
		$router = Seven::app()->getRouter();
		$node = $router->getRoutingNode($path);
		$this->_addSitemapItems($node, $deep);
		return $this;
	}
	
	protected function _getCurrentPaths() {
		static $paths = null;
		if(empty($paths)) {
			$current_url = trim(Seven::app()->getRequest()->getController() . "/" . Seven::app()->getRequest()->getAction(), '/');
			$path = "";
			foreach(explode('/', $current_url) as $part) 
				$paths[] = $path ? ($path . "/" . $part) : $part;
		}
		return $paths;
	}
	
	protected function _addSitemapItems($node, $deep = 0, $parent = 'root') {
		$paths = $this->_getCurrentPaths();
		foreach($node->getChildren() as $child) {
			if($child->getConfig('hidden')) continue;
			$url = ltrim($child->getRoute(), '/');
			$this->addItem($child->getId(), array(
				'label' => $child->getConfig('name'),
				'href'  => seven_url("*/" . $url),
				'order' => $child->getConfig('order'),
			), $parent);

			if($deep > 0)
				$this->_addSitemapItems($child, $deep - 1, $child->getId());
			if(in_array($url, $paths))
				$this->setActiveItem($child->getId());
		}		
	}
	
}