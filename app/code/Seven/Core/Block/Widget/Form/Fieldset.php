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

class Core_Block_Widget_Form_Fieldset extends Core_Block_Template {
	
	public function __construct($data = array()) {
		if(empty($data['template']))
			$data['template'] = 'widgets/form/fieldset.phtml';
		parent::__construct($data);
	}
	
	public function addInput($id, $field) {
		if($field instanceof Core_Model_Form_Input_Abstract) {
			$data = $field->getData();
			$data['input_model'] = $field;
			$field = ($attribute = $field->getAttributeModel()) ? 
					$attribute->getInputWidget($data) : 
					Seven::getBlock($field->getBlockAlias(), $data);
		}
		
		if(!$field->getHtmlName())
			$field->setHtmlName($field->getName() ? $field->getName() : $id);
			
		$field->setLayoutName($this->getLayoutName() . "." . $id)
              ->setLayout($this->getLayout())
			  ->prepare();
		return $this->addChild($id, $field);
	}

}