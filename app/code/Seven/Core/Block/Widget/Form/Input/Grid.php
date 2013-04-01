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

	class Core_Block_Widget_Form_Input_Grid extends Core_Block_Widget_Grid_Xml {

		public function __construct($data = array()) {
			if(!isset($data['add_default_actions']))
				$data['add_default_actions'] = 0;
			if(!isset($data['ajax']))
				$data['ajax'] = true;
			if(!isset($data['template']))
				$data['template'] = 'widgets/form/input/grid.phtml';
			parent::__construct($data);
			if(isset($data['alias']))
				$this->setAlias($data['alias']);
			if(isset($data['fields']))
				$this->_initFields();
		}
		
		public function setAlias($alias) {
			$this->initXml($alias);
			return parent::setAlias($alias);
		}
		
		public function addColumn($id, $data = array()) {
			parent::addColumn($id, $data);
			$this->_columns[$id]->setInputModel($this->getInputModel()->getField($id));
			return $this;
		}
		
	}
