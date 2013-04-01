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
 * @package    Access
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

class Access_Block_Widget_Form_Input_Accesstree_Node extends Core_Block_Template {

	public function __construct($data = array()) {
		if(!isset($data['template']))
			$data['template'] = 'Seven/Access/widget/accesstree_node.phtml';
		parent::__construct($data);
	}
	
	public function prepare() {
		foreach((array)$this->getData('routes') as $name => $data) {
			if(!empty($data['access'])) continue;
			$this->addChild($this->getLayoutName() . "." . $name, $block = Seven::getBlock('access/widget_form_input_accesstree_node', $data));
			$block->setLayoutName($this->getLayoutName() . "." . $name)
					->setTreePath(rtrim($this->getTreePath(), '/') . "/" . $name)
					->setHtmlName($this->getHtmlName())	
					->setFlatName($this->getFlatName())
					->setInputModel($this->getInputModel())
					->prepare();	
		}	
		return parent::prepare();
	}
	
	public function getInputHtml() {
		$values = new Seven_Object($this->getInputModel() ? $this->getInputModel()->getValue() : array());
		$html_name = $this->getFlatName() ? ($this->getHtmlName() . "[" . $this->getTreePath() . "]") : ($this->getHtmlName() . "[" . str_replace('/', '][', trim($this->getTreePath(), '/')) . "][_value]");
		return Seven::getBlock('core/widget_form_input_select', array(
					'html_name' => $html_name, 
					'value' => $values->getData($this->getTreePath()), 
					'options' => $this->getOptions(),
					'html_classes' => 'permission-selector'				
		))->toHtml();
	}

	public function getOptions() {
		if($this->getParent() instanceof self)
			return array('' => __('Inherit'), 'allow' => __('Allow'), 'deny' => __('Deny'));
		return array('allow' => __('Allow'), 'deny' => __('Deny'));
	}
	
}