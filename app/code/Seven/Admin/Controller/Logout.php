<?php

	class Admin_Controller_Logout extends Core_Controller_Abstract {
		
		public function indexAction() {
			Seven::getModel('admin/session')->logout();
			throw new Core_Exception_Redirect(seven_url(':admin_login'));
		}
		
	}