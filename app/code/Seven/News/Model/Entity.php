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
 * @package    News
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

	class News_Model_Entity extends Core_Model_Multiview_Entity {

		public function getUrl() {
			return seven_url('*/news/' . $this->_getId());
		}

		public function save() {
			return parent::save();
		}
		
		public function _getDefaultView() {
			return "";
		}
		
	}