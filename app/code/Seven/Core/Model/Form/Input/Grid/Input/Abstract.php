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

	class Core_Model_Form_Input_Grid_Input_Abstract extends Core_Model_Form_Input_Abstract {
	
        protected function _getSource() {
            return $this->getParent()->getSource();
        }
	
		public function getRowValue($row) {
			$values = $this->getValue();
			return isset($values[$row]) ? $values[$row] : null; 
		}
				
	}