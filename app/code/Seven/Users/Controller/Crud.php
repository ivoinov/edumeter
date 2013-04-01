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
 * @package    Users
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

	class Users_Controller_Crud extends Core_Controller_Crud {
		
		protected function _saveFormEdit($options) {
			$form   = $options->getEditForm();
			if(!$form->getField('password')->getValue()) {
				$form->removeField('password');
			}
			return parent::_saveFormEdit($options);
		}
	
		protected function _loadFormEdit($options) {
			parent::_loadFormEdit($options);
			$form   = $options->getEditForm();
			if($input = $form->getField('password'))
				$input->setRequired(false);
			return $this;
		}
			
	}