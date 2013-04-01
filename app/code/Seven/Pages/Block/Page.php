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
 * @package    Pages
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

class Pages_Block_Page extends Core_Block_Widget_View {
	
	protected function _getView() {
		if($this->getPageId())
			return Seven::getModel('pages/page')->load($this->getPageId());
		if($this->getPageTitle())
			return Seven::getModel('pages/page')->load($this->getPageTitle(), 'title');
		return parent::_getView();
	}

}