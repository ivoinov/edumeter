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

class Core_Block_Widget_Grid_Column_Date extends Core_Block_Widget_Grid_Column_Text {

	protected $_filter_blocks = array();
	
	public function prepare() {
		$filter = new Seven_Object((array)$this->getFilterValue());
		$this->_filter_blocks = array(
			Seven::getBlock('core/widget_form_input_date')
                ->setParent($this)
				->setHtmlName('filter[' . $this->getIndex() . '][from]')
				->addHtmlClass('widget-grid-filter')
				->addHtmlClass('widget-grid-filter-from')
				->setWrapper('.widget-grid-filter-from-holder')
				->setValue($filter->getFrom())
				->setPlaceholder(__('From')),		
			Seven::getBlock('core/widget_form_input_date')
                ->setParent($this)
				->setHtmlName('filter[' . $this->getIndex() . '][to]')
				->addHtmlClass('widget-grid-filter')
				->addHtmlClass('widget-grid-filter-to')
				->setWrapper('.widget-grid-filter-to-holder')
				->setValue($filter->getTo())
				->setPlaceholder(__('To'))
		);

		foreach($this->_filter_blocks as $filter)
			$filter->prepare();
			
		return parent::prepare();
	}
	
	protected function _getFilterHtml() {
		$html = "";
		foreach($this->_filter_blocks as $filter)
			$html .= $filter->toHtml();
		return $html;
	}

	protected function _apply($collection, $grid) {
		if($this->getFilterValue()) {
			$filter = new Seven_Object($this->getFilterValue());
			$condition = array();
                        $fieldname = $this->getTable() ? ($this->getTable() . "." . $this->getIndex()) : $this->getIndex();                                                    
			if($filter->getTo() !== null && $filter->getTo() !== '')
				$condition['to'] = $filter->getTo();
			if($filter->getFrom() !== null && $filter->getFrom() !== '')
				$condition['from'] = $filter->getFrom();
			if($condition)
				$collection->filter($fieldname, $condition);
		}
	}

	public function getCellValue(Seven_Object $row) {
		if($time = strtotime($value = $this->_getRawCellValue($row)))
			$value = date(__("DATEFORMAT::m/d/Y"), $time);
		if($this->hasOutputCallback())
			return call_seven_callback_array($this->getOutputCallback(), array(htmlspecialchars($value), $row), 'helper');
		return htmlspecialchars($value);
	}

}