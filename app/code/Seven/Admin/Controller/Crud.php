<?php

	class Admin_Controller_Crud extends Core_Controller_Crud {

		protected function _loadFormEdit($options) {
			$options = $this->_extendControllerOptions($options);
			parent::_loadFormEdit($options);
			$form   = $options->getEditForm();
			if($password = $form->getField('password')) {
				$password->setRequired(false);
			}
		}
		
		protected function _saveFormEdit($options) {
			$options = $this->_extendControllerOptions($options);
			$form   = $options->getEditForm();
			
			if(($password = $form->getField('password')) && !$password->getValue()) 
				$form->removeField('password');
			
			parent::_saveFormEdit($options);
		}

		protected function _prepareDelete($options = array()) {
			$options = $this->_extendControllerOptions($options);
			parent::_prepareDelete($options);
			$entity = $options->getDeleteEntity();
			if($entity->_getId() == Seven::getSingleton('admin/session')->getAdmin()->_getId())
				throw new Core_Exception_Forbidden('You can\'t delete your own account');
			return $entity;
		}
		
	}