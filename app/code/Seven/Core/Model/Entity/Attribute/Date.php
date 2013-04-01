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

	class Core_Model_Entity_Attribute_Date extends Core_Model_Entity_Attribute_Abstract {

		protected $_data_type   = 'date';
		
		public function formatValue($value, $options = array()) {
			if(empty($options['format']))
				$options['format'] = __("DATEFORMAT::m/d/Y");
			return parent::formatValue(date($options['format'], strtotime($value)), $options);
		}
		
	}