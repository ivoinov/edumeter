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

abstract class Core_Block_Widget_Grid_Filter_Abstract extends Core_Block_Template {
	
	abstract public function apply($collection, $grid);
	
	protected function getFilterValue() {
		return Seven::app()->getRequest()->getParam($this->getRequestVarName());
	}

	public function getFilterHtml() {
		return $this->render($this->getTemplate());
	}
	
}