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

    class Core_Block_Widget_Menu extends Core_Block_Template {
        
        protected $_items = array();
        protected $_reserved = array();
        protected $_root  = array();
        
        public function __construct($data = array()) {
            if(!isset($data['template']))
                $data['template'] = 'widgets/menu.phtml';
            parent::__construct($data);
            $this->_items['root'] = $this->_root = Seven::getBlock("core/widget_menu_item")->setParent($this);
        }  

        protected function _setActiveItem($id, $reset = true) {
            if($reset)
                foreach($this->_items as $item)
                    $item->setActive(0);
            $this->_setActive($id);
        }
        
        protected function _setActive($id, $subactive = false) {
            if($item = $this->getItem($id)) {
                $item->setActive($subactive ? 2 : 1);
                $this->_setActive($item->getParentId(), false);
            }
        }
        
        public function _getItemClass($data) {
            if(isset($data['class']))
                return $data['class'];
            if(!isset($data['type']))
                return 'core/widget_menu_item';
            if(strpos($data['type'], '/') === false)
                $data['type'] = "core/" . $data['type'];
            list($package, $name) = explode('/', $data['type'], 2);
            return $package . '/widget_menu_' . $name;
        }
        
        public function addItem($id, $data, $parent = 'root') {
            $children = empty($data['children']) ? array() : $data['children'];
            $data['parent_id'] = $parent;
            if(!isset($this->_items[$id]))
	            $this->_items[$id] = Seven::getBlock($this->_getItemClass($data), $data)->setParent($this);
            if(isset($this->_items[$parent])) {
                $this->_items[$parent]->addChild($id, $this->_items[$id]);
                if(isset($this->_reserved[$id]))
                	foreach($this->_reserved[$id] as $child_id) {
                		$this->_items[$id]->addChild($child_id, $this->_items[$child_id]);
                }
        	} else { 
        		if(!isset($this->_reserved[$parent]))
        			$this->_reserved[$parent] = array();
            	$this->_reserved[$parent][] = $id;
        	}
            if(!empty($children))
                $this->_addChildren($id, $children);
            return $this;
        }

        protected function _addChildren($parent, $data) {
            foreach($data as $key => $item)
                $this->addItem($parent . "." . $key, $item, $parent);
            return $this;
        }

        public function toHtmlMenu($root) {
            $this->_root = $root;
            $level = $this->getLevel();
            $this->setLevel($level + 1);
            $html = $this->_toHtml();
            $this->setLevel($level);
            return $html;
        }
        
        public function removeItem($id) {
            if(!isset($this->_items[$id])) {
                $parent = $this->_items[$id]->getParentId();
                if(isset($this->_items[$parent]))
                    $this->_items[$parent]->getChildren()->offsetUnset($id);
                unset($this->_items[$id]);
            }
            return $this;
        }
        
        public function getItems() {
            return $this->_root->getSortedChildren();
        }

        public function getItemsTree() {
            return $this->_root->getSortedChildren();
        }
        
        public function getItem($id) {
            if(isset($this->_items[$id]))
                return $this->_items[$id];
            return NULL;
        }

        public function prepare() {
            $this->_setActiveItem($this->getActiveItem());
            return parent::prepare();
        }
        
    }