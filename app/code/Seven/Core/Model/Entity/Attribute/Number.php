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

	class Core_Model_Entity_Attribute_Number extends Core_Model_Entity_Attribute_Abstract {

		protected $_data_type   = 'int';
		
		public function formatValue($value, $options = array()) {
			return sprintf($this->getFormat() ?: "%d", parent::formatValue($value, $options));
		}
		
	}