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

class Core_Block_Widget_Grid_Xml extends Core_Block_Widget_Grid {
	
	public function initXml($alias) {
		if(strpos($alias, "/") === false)
			$alias = "core/" . $alias;
		list($module, $form_name) = explode("/", $alias, 2);
		$this->addData($data = Seven::getConfig('grids/' . $module . '/' . $form_name));
		if($data === NULL)
			throw new Exception("XML Grid node '" . $alias . "' not exists");
		
		$this->_initFields();
		return $this;
	}
	
	protected function _initFields() {
		if(is_array($this->getData('fields'))) {
			foreach($this->getData('fields') as $key => $data) {
				if(!empty($data['ignore'])) continue;
				$data = $this->_extendColumnData($key, $data);
				$this->addColumn($key, $data);
			}
		}
	}
	
	protected function _extendColumnData($key, $data) {
		if(! isset($data['source']) && $this->hasSource())
			$data['source'] = $this->getSource() . "/" . $key;
		if(! isset($data['index']))
			$data['index'] = $key;
		
		$source_parts = isset($data['source']) ? explode("/", $data['source'], 3) : array();
		
		if(count($source_parts) == 3) {
			list($module, $entity, $field) = $source_parts;
			$xml = Seven::getResource("{$module}/{$entity}")
							->getFieldOptions($field);
			$data = array_merge_recursive_replace($xml->getData(), $data);
		}
		return $data;
	}
	
	public function prepare() {
		if($this->getAddDefaultActions() === NULL || $this->getAddDefaultActions()) {
			$this->addColumn('actions', array('type' => 'action', 'actions' => array('edit' => array('label' => __('Edit'), 'url_template' => '*/*/edit?id={id}', 'skin_icon' => 'images/icons/pencil.png'), 'delete' => array('label' => __('Delete'), 'url_template' => '*/*/delete?id={id}', 'skin_icon' => 'images/icons/cross.png'))));
		}
		return parent::prepare();
	}
	
	protected function _getCollection() {
		try {
			if($collection = parent::_getCollection())
				return $collection;
		} catch(Core_Exception_Layout $e) {
			
		}
		/** @deprecated since 1.0.0 use registry instead */
		$collection = $this->hasData('collection') ? $this->getData('collection') : $this->getSource();
		if(! $collection)
			throw new Exception('You should specify collection for gird in XML');
		return Seven::getCollection($collection);
	}

}