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
 * @package    Static
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

class Static_Model_Block extends Core_Model_Multiview_Entity {
	
	protected $_options = null;
	
	public function getOptionsArray() {
		if($this->_options === null) {
			$this->_options = array();
			foreach($this->getCollection()->load() as $block) {
				$this->_options[$block->_getId()] = $block->getTitle() ?: $block->_getId();
			}
		}
		return $this->_options;
	}
	
}