<?php 

	class Core_Helper_Validators {
		
		public function unique($value, $input) {
			if(!($form = $input->getForm()))
				throw new Core_Exception_Error('Unable to get form object');
			$entity = $form->getEntity();
			if(!($entity instanceof Core_Model_Entity))
				throw new Core_Exception_Error('Unable to check unique value');

			if($entity->getData($input->getName()) == $value)
				return true;
				
			if(!$entity->_getResource()->isUniqueValue($input->getName(), $value)) {
				$input->setErrorMessage(__('Value should be unique'));
				return false;
			}
			return true;
		}
		
		public function email($value, $input) {
			if(!preg_match('/^(?:[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])$/', $value)){
				$input->setErrorMessage(__('E-Mail has incorrect format'));
				return false;
			}
			return true;
		}
		
		public function confirm($value, $input) {
			if($input->getForm() && ($confirm = ($input->getForm()->getField($input->getConfirmTo())))) {
				if($input->getValue() != $confirm->getValue()) {
					$input->setErrorMessage(__('Value not confirmed'));
					return false;
				}
			} else throw new Exception("Unable to get confirm input field");
			return true;
		}
		
	}