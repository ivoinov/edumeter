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

class Core_Block_Widget_Grid_Column_Select extends Core_Block_Widget_Grid_Column_Abstract {

	protected function _apply($collection, $grid) {
		if($this->getFilterValue() !== null && $this->getFilterValue() != '') {
                        $fieldname = $this->getTable() ? ($this->getTable() . "." . $this->getIndex()) : $this->getIndex();
        		$collection->filter($fieldname, $this->getFilterValue());
		}
	}
	
	public function getOptions() {
		$options = parent::getOptions();
		if(! is_array($options)) {
			$method = "getOptionsArray";
			if(strpos($options, "::") !== false)
				list($options, $method) = explode("::", $options);
			$options = call_user_func(array(Seven::getModel($options), $method));
			parent::setOptions($options);
		}
		return $options;
	}
	
	public function getCellValue(Seven_Object $row) {
		$value = parent::getCellValue($row);
		$options = $this->getOptions();
		if(isset($options[$value]))
			return $options[$value];
		return $value;
	}
	
	protected function _getFilterHtml() {
		$options = array_merge_recursive_replace(array('' => null), $this->getOptions());
		return Seven::getBlock('core/widget_form_input_select')
                ->setParent($this)
				->setHtmlName('filter[' . $this->getIndex() . ']')
				->addHtmlClass('widget-grid-filter')
				->setOptions($options)
				->setValue($this->getFilterValue())
				->toHtml();
	}
	

}