<?php

	class Admin_Controller_Login extends Core_Controller_Abstract {
		
		public function indexAction() {
			$form = Seven::getModel('core/form_xml')
						->initXml('admin/login')
						->load();
							
			if($form->isSubmit()) {
				if($form->isValid()) {
					if(Seven::getModel('admin/session')->login($form->getValues())) {
						Seven::getSingleton('core/session')->addSuccess(__('You are successfully logged in'));
						throw new Core_Exception_Redirect(seven_url('*'));
					} else {
						Seven::getSingleton('core/session')->addError(__('Your e-mail or password does not match'));				
					}
				}
			}
			
			Seven::register('login_form', $form);
			
			$this->getLayout()
					->removeTag('default')
					->addTag('_login');
					
			$this->render();
		}
		
	}