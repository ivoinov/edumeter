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

class Core_Block_Widget_Grid_Column_Abstract extends Core_Block_Widget_Grid_Filter_Abstract {
	
	public function __construct($data = array()) {
		if(!isset($data['filterable']))
			$data['filterable'] = true;
		parent::__construct($data);
	}
	
	public function getFilterValue() {
		$data = new Seven_Object(Seven::app()->getRequest()->getParam('filter'));
		return $data->getData($this->getIndex());
	}
	
	public function getHtmlAttributes() {
		$attributes = parent::getHtmlAttributes();
		if($this->getWidth())
			$attributes['width'] = $this->getWidth();
		unset($attributes['id']);
		return $attributes;
	}
	
	public function getHtmlAttributeString() {
		$attributes = "";
		foreach($this->getHtmlAttributes() as $key => $value)
			if($value && $key != "id") // skip empty attributes and ID
				$attributes .= " " . ($value ? ($key . " = '" . $value . "'") : $key);
		return $attributes;
	}
	
	protected function _getRawCellValue(Seven_Object $row) {
		$use_raw = $this->getUseRawValue() || !($row instanceof Core_Model_Entity);
		return $use_raw ? $row->getData($this->getIndex()) : $row->formatData($this->getIndex());
	} 
	
	public function getCellValue(Seven_Object $row) {
		$value = $this->_getRawCellValue($row);
		if($this->hasOutputCallback())
			return call_seven_callback_array($this->getOutputCallback(), array($value, $row), 'helper');
		return $value;
	}
	
	public function _toHtml() {
		return $this->getLabel();
	}

	final public function apply($collection, $grid) {
		if(!$this->getFilterable()) return false;
		$this->_apply($collection, $grid);
	}
	
	protected function _apply($collection, $grid) {
		if($this->getFilterValue() !== null && $this->getFilterValue() != '') {
                    $fieldname = $this->getTable() ? ($this->getTable() . "." . $this->getIndex()) : $this->getIndex();
                    $collection->filter($fieldname, array('like' => '%' . $this->getFilterValue() . '%'));
		}
	}
        
        public function getTable() {
            if($table = parent::getTable()) {
                if(strpos($table, '/') !== false) {
                    $this->setTable($table = Seven::getResource($table)->getTable());                    
                }
            }
            return $table;
        }
	
	final public function getFilterHtml() {
		if(!$this->getFilterable()) return "";
		return $this->_getFilterHtml();
	}
	
	protected function _getFilterHtml() {
		return Seven::getBlock('core/widget_form_input_text')
                ->setParent($this)
				->setHtmlName('filter[' . $this->getIndex() . ']')
				->addHtmlClass('widget-grid-filter')
				->setValue($this->getFilterValue())
				->toHtml();
	}
	
}
