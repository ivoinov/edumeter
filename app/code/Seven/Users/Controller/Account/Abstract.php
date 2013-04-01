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

/**
 * Abstract User Account controller. Abstract actions:
 * 	- login
 *  - logout
 *  - edit   (change profile)
 *  - view   (view profile)
 *  - delete (delete own profile)
 */

abstract class Users_Controller_Account_Abstract extends Core_Controller_Crud_Abstract {
	
	public function getUse() {
		return 'users/user';
	}
	
	protected function _getDefaultOptions() {
		return array_merge($this->getData(), parent::_getDefaultOptions());
	}
	
	protected function _loginAbstract($options = array()) {
		$options = $this->_extendControllerOptions($options);
		$this->_prepareLogin($options);
		$this->_processLogin($options);
		$this->_renderLogin($options);
	}
	
	protected function _prepareLogin($options) {
		$form = Seven::getModel($options->getLoginFormType() ?: 'core/form_xml')
					->initXml($options->getLoginFormInit() ?: 'users/login')
					->load();
		$options->setLoginForm($form);
		Seven::register($options->getLoginRegistry() ? : 'login_form', $form); 
		return $form;
	}
	
	protected function _processLogin($options) {
		$form = $options->getLoginForm();
						
		if($form->isSubmit()) {
			if($form->isValid()) {
				if(Seven::getModel('users/session')->login($form->getValues())) {
					if($options->getLoginSuccessMessage() !== false)
						Seven::getSingleton('core/session')->addSuccess($options->getLoginSuccessMessage() ?: __('You are successfully logged in'));
					return $this->_afterLogin($options);
				} else {
					if($options->getLoginFailMessage() !== false)
						Seven::getSingleton('core/session')->addError($options->getLoginFailMessage() ?: __('Your e-mail or password does not match'));				
				}
			}
		}

		return $form;
	}
	
	protected function _renderLogin($options) {
		if($options->getLoginSingleForm())
			$this->getLayout()->removeTag('default');
		$this->getLayout()->addTag('login_page');
		$this->loadLayout();
		$this->renderLayout();				
	}
	
	protected function _afterLogin($options) {
		if($options->getLoginRedirect() !== false)
			$this->redirect($options->getLoginRedirect() ?: seven_url('*/'));
		return $this;
	}
	
	protected function _logoutAbstract($options = array()) {
		$options = $this->_extendControllerOptions($options);
		Seven::getSingleton('users/session')->logout();
		$this->_afterLogout($options);	
	}
	
	protected function _afterLogout($options) {
		if($options->getLogoutRedirect() !== false)
			$this->redirect($options->getLogoutRedirect() ? : seven_url('*/'));
		return $this;
	}	

	protected function _getEntity($id = null, $options = array()) {
		return Seven::getSingleton('users/session')->getUser();
	}
		
	protected function _loadFormEdit($options) {
		parent::_loadFormEdit($options);
		$form   = $options->getEditForm();
		if($password = $form->getField($options->getEditFormPasswordField() ?: 'password'))
			$password->setRequired(false);
	}
	
	protected function _saveFormEdit($options) {
		$name = $options->getEditFormPasswordField() ?: 'password';
		if(($password = $form->getField($name)) && !$password->getValue())
			$form->removeField($name);			
		parent::_saveFormEdit($options);
	}
	
}
