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

	abstract class Core_Block_Widget_Grid_Column_Input_Abstract extends Core_Block_Widget_Grid_Column_Abstract {

		public function getCellValue(Seven_Object $row) {
			$input_widget = $this->_createInputBlock();
			$htmlname = $this->getInputModel() ? $this->getInputModel()->getHtmlName() : $this->getHtmlName();
			$input_widget->setHtmlName($htmlname . "[" . $row->_getId() . "]");
			$input_widget->setValue($this->getInputModel() ? $this->getInputModel()->getRowValue($row->_getId()) : false);			
			return $input_widget->toHtml();	
		}

		abstract protected function getType();

		protected function _createInputBlock() {
			return Seven::getBlock('core/widget_form_input_' . $this->getType(), $this->getData())->setParent($this);
		}
				
	}