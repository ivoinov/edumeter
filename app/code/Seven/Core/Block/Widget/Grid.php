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

class Core_Block_Widget_Grid extends Core_Block_Template {
	
	protected $_filters_sorted = true;
	
	public function __construct($data = array()) {
		if(! isset($data['wrapper_template']))
			$data['wrapper_template'] = 'widgets/grid/wrapper.phtml';
		if(! isset($data['template']))
			$data['template'] = 'widgets/grid.phtml';
			if(! isset($data['page_size']))
			$data['page_size'] = 20;
		parent::__construct($data);
		if($this->getPager() === null || $this->getPager())
			$this->addFilter("pager", array('type' => 'pager', 'filter_priority' => 1000));
		$this->addHtmlClass('widget-grid');
	}
	
	public function prepare() {
		if($head = $this->getLayout()->getBlock('head'))
			$head->addJs('seven/widgets/grid.js', 'lib');
		foreach($this->_columns as $column) 
			$column->prepare();
		$this->applyFilters();
		return parent::prepare();
	}
	
	protected $_collection = null;
	
	public function getCollection() {
		if(! $this->_collection) {
			$this->_collection = $this->_getCollection();
		}
		return $this->_collection;
	}
	
	public function applyFilters() {
		// apply filters
		foreach($this->getSortedFilters() as $filter) {
			//var_dump(get_class($filter));
			$filter->apply($this->getCollection(), $this);
		}
		// apply sort
		if($this->getSort()) {
			$this->getCollection()->order(array_values((array) $this->getSort()));
		}
	}
	
	protected $_columns = array();
	
	public function addColumn($id, $data = array()) {
		if(isset($data['column_class'])) {
			$block_class = $data['column_class'];
		} else {
			if(empty($data['type']))
				$data['type'] = 'core/text';
			if(strpos($data['type'], '/') === false)
				$data['type'] = 'core/' . $data['type'];
			list($package_id, $column_type) = explode('/', $data['type'], 2);
			$block_class = $package_id . '/widget_grid_column_' . $column_type;
		}
		$this->_columns[$id] = Seven::getBlock($block_class, $data)->setParent($this);
		if($this->_columns[$id]->getFilterable() !== false)
			$this->_filters[$id] = $this->_columns[$id];
		return $this;
	}
	
	public function removeColumn($id) {
		unset($this->_columns[$id]);
		return $this;
	}

	protected function _toAjax() {
		return $this->getInnerHtml();
	}
	
	public function getColumn($key) {
		if(isset($this->_columns[$key]))
			return $this->_columns[$key];
		return null;
	}
	
	public function getCellValue($column_id, $row) {
		if($column = $this->getColumn($column_id))
			return $column->getCellValue($row);
		return '';
	}
	
	public function getColumns() {
		return $this->_columns;
	}
	
	protected $_filters = array();
	
	public function addFilter($id, $data = array()) {
		$this->_filters_sorted = false;
		$data['grid'] = $this;
		if(isset($data['filter_class'])) {
			$block_class = $data['filter_class'];
		} else if(!empty($data['type'])) {			
			if(strpos($data['type'], '/') === false)
				$data['type'] = 'core/' . $data['type'];
			list($package_id, $filter_type) = explode('/', $data['type'], 2);
			$block_class = $package_id . '/widget_grid_filter_' . $filter_type;
		} else { 
			throw new Exception("The " . $id . " filter type not specified for '" . get_class($this) . "' grid");
		}		
		$this->_filters[$id] = Seven::getBlock($block_class, $data)->setParent($this);
		return $this;
	}
	
	public function removeFilter($id) {
		unset($this->_filters[$id]);
		return $this;
	}
	
	public function getFilters() {
		return $this->_filters;
	}
	
	public function getFilter($id) {
		if(isset($this->_filters[$id]))
			return $this->_filters[$id];
		return NULL;
	}

	public function setColumnProperty($column_id, $key, $value) {
		if($column = $this->getColumn($column_id))
			$column->setData($key, $value);
	}
	
	public function callColumnMethod($column_id, $method, $arguments = array()) {
		if($column = $this->getColumn($column_id))
			return call_user_func_array(array($column, $method), $arguments);
	}
	
	public function getInnerHtml() {
		$this->getCollection()->load(); // load collection before loading
		return $this->render($this->getTemplate());
	}
	
	protected function _toHtml() {
		if($this->getWrapperTemplate())
			return $this->render($this->getWrapperTemplate());
		return $this->getInnerHtml();
	}
	
	public function getFilterable() {
		return parent::getFilterable() === NULL || parent::getFilterable();
	}
	
	protected function _getCollection() {
		if(!$this->getRegistryName())
			throw new Core_Exception_Layout('Registry name for view widget not specified');
		return Seven::registry($this->getRegistryName());
	}
	
	public function getSortedFilters() {
		if(!$this->_filters_sorted) {
			$this->_filters_sorted = $this->_filters;
			usort($this->_filters_sorted, function($a, $b) {
				if($a->getFilterPriority() < $b->getFilterPriority()) return -1;
				if($a->getFilterPriority() == $b->getFilterPriority()) return 0;
				return 1;
			});
		}
		return is_array($this->_filters_sorted) ? $this->_filters_sorted : array();
	}
	
}
