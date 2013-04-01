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

	class Core_Model_Entity_Attribute_Decimal extends Core_Model_Entity_Attribute_Number {

		protected $_data_type   = 'decimal';
		
		public function formatValue($value, $options = array()) {
			return sprintf($this->getFormat() ?: "%f", parent::formatValue($value, $options));
		}
		
		public function getPrecision() {
			return parent::getPrecision() ? : 12;
		} 
		
		public function getScale() {
			return parent::getScale() ? : 4;
		} 
		
		protected function _getBlock() {
			return 'core/widget_form_input_number';
		}
		
		protected function _getModel() {
			return 'core/form_input_number';
		}
		
	}