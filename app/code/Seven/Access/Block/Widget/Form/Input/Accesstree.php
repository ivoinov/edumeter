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

	class Access_Block_Widget_Form_Input_Accesstree extends Core_Block_Template {
		
		public function __construct($data = array()) {
			if(!isset($data['template']))
				$data['template'] = 'Seven/Access/widget/accesstree.phtml';
			parent::__construct($data);
		}
		
		public function prepare() {
			if($head = $this->getLayout()->getBlock('head')) {
				$head->addCss('access/default.css', 'skin');
			}
			$this->addChild('root_node', $block = Seven::getBlock('access/widget_form_input_accesstree_node', $this->getRoot()));
			$block->setLayoutName($this->getLayoutName() . '.root_node')
					->setHtmlName($this->getHtmlName())
					->setFlatName($this->getFlatName())
					->setInputModel($this->getInputModel())
					->setTreePath('/')
					->prepare();
			return parent::prepare();
		}
		
		public function getRoot() {
			return array('name' => __('Default policy'), 'routes' => $this->getInputModel()->getRoot());
		}

		
	}