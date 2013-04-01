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

class Core_Block_Widget_Form_Tabs extends Core_Block_Template {
	
	public function __construct($data = array()) {
		if(empty($data['template']))
			$data['template'] = 'widgets/form/tabs.phtml';
		parent::__construct($data);
	}
	
	public function addGetTab($label) {
		static $id = 0;
		foreach($this->getChildren() as $name => $child)
			if($child->getLabel() == $label)
				return $child;
		return $this->addChild($name = 'tab' . $id++, Seven::getBlock('core/widget_form_tabs_tab', array('label' => $label))->setLayoutName($this->getLayoutName() . "." . $name)->prepare());
	}
	
	public function prepare() {
		if($head = $this->getLayout()->getBlock('head'))
			$head->addCss('tabs.css', 'skin')->addJs('js/tabs.js', 'skin');
		parent::prepare();
		return $this;
	}
}
