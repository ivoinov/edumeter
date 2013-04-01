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

class Core_Block_Widget_Form extends Core_Block_Widget_Container {
	
	protected $_inputs = array();
	
	public function __construct($data = array()) {
		if(! isset($data['template']))
			$data['template'] = 'widgets/form.phtml';
		if(! isset($data['method']))
			$data['method'] = 'POST';
		if(! isset($data['buttons_bottom']))
			$data['buttons_bottom'] = true;
		if(! isset($data['buttons_top']))
			$data['buttons_top'] = false;
		if(! isset($data['enctype']))
			$data['enctype'] = 'multipart/form-data';
		parent::__construct($data);
		$this->addButton("submit", array('type' => 'submit', 'label' => __('Submit')));
	}
	
	public function setModel($model) {
		$this->addData(array_merge(array('method' => 'POST', 'fieldset' => null, 'tab' => null), $model->getData(), $this->getData()));
		return parent::setModel($model);
	}
	
	public function getHtmlAttributes() {
                $attributes = parent::getHtmlAttributes();
                $attributes['action'] = seven_url($this->getAction());
                $attributes['method'] = $this->getMethod(); 
                $attributes['name'] = $this->getName();
                $attributes['enctype'] = $this->getEnctype();
                if(!$this->getAction())
                        $attributes['action'] = '';
                return $attributes;
	
	}
	
	public function prepare() {
		if($this->getRegistryName()) {
			if(!Seven::registered($this->getRegistryName()))
				throw new Core_Exception_Layout('Form registry not specified');
			$form = Seven::registry($this->getRegistryName());
			if(!($form instanceof Core_Model_Form))
				throw new Core_Exception_Layout('Form registry is not an valid form object');
			$this->setModel($form);
		}
		$this->_autofill();
		return parent::prepare();
	}
	
	protected function _getFieldsetData($name) {
		$data = new Seven_Object();
		$fieldsets = $this->getFieldsets();
		if(isset($fieldsets[$name]))
			$data->addData((array)$fieldsets[$name]);
		if(!$data->hasType())
			$data->setType('core/widget_form_fieldset');
		//if(!$data->hasLabel())
		//	$data->setLabel($name);
		$data->setParent($data->hasTab() ? $this->_addGetTab($data->getTab()) :  $this);
		return $data;
	}
	
	protected function _getTabData($name) {
		$data = new Seven_Object();
		$tabs = $this->getTabs();
		if(isset($tabs[$name]))
			$data->addData((array)$tabs[$name]);
		if(!$data->hasType())
			$data->setType('core/widget_form_tabs_tab');
		if(!$data->hasLabel())
			$data->setLabel($name);
		$data->setParent($this->_addGetTabset($data->getTabset() ? : 'tabset'));
		return $data;
	}
	
	protected function _addGetTabset($name) {
		$name = (string) (empty($name) ? 'tabset' : $name);
		if(!($tabset = $this->getChild($name))) {
			$tabset = Seven::getBlock('core/widget_form_tabs');
			$this->addChild($name, $tabset);
		}
		return $tabset;
	}
	
	protected function _addGetTab($name) {
		$name = (string) (empty($name) ? 'default' : $name);
		$data = $this->_getTabData($name);
		if(!($tab = $data->getParent()->getChild($name))) {
			$tab = Seven::getBlock($data->getType(), $data->getData());
			$data->getParent()->addChild($name, $tab);
		} else {
			$tab->addData($data->getData());
		}
		return $tab;
	}
	
	protected function _addGetFieldset($name) {
		$name = (string) (empty($name) ? 'default' : $name);
		$data = $this->_getFieldsetData($name);
		if(!($fieldset = $data->getParent()->getChild($name))) {
			$fieldset = Seven::getBlock($data->getType(), $data->getData());
			$data->getParent()->addChild($name, $fieldset);
		} else {
			$fieldset->addData($data->getData());
		}
		return $fieldset;
	}
	
	protected function _autofill() {
		if($this->getModel()) {
			foreach($this->getModel()->getFields() as $name => $field) {
				$this->_inputs[$name] = $this->_addGetFieldset($field->getFieldset() ?: $this->getFieldset())
											->addInput($name, $field);
			}
		}
	}
	
	public function getInputs() {
		return $this->_inputs;
	}
	
	public function getInputHtml($name) {
		return $this->_inputs[$name]->_toHtml();
	}
	
	protected function _toHtml() {
		return "<form " . $this->getAttributeString() . ">" . parent::_toHtml() . "</form>";
	}
	
	protected function _toAjax() {
		return $this->_toHtml();
	}
	
	public function setTitle($title, $tag = "h1") {
		parent::setTitleTag($tag);
		return parent::setTitle($title);
	}
	
}