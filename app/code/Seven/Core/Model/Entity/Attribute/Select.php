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

	class Core_Model_Entity_Attribute_Select extends Core_Model_Entity_Attribute_Abstract {

		public function formatValue($value, $options = array()) {
			$options = $this->getOptions();
			if(isset($options[$value]))
				$value = $options[$value];
			return parent::formatValue($value, $options);
		}
		
		public function getOptions() {
			$options = Seven_Object::getOptions();
			if(!is_array($options)) {
		            $method = "getOptionsArray";
		            if(strpos($options, "::") !== false)
		                list($options, $method) = explode("::", $options);
			    $options = call_user_func(array(Seven::getModel($options), $method), $this);
			    Seven_Object::setOptions($options);
			}
			return $options;
		}
		
	}