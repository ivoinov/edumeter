<?php 

	class Core_Model_Mail extends Zend_Mail {

		protected $_template_vars = array();
		protected $_template_id   = null;
	
		public function __construct() {
			$this->setFrom(Seven::getSiteConfig('mail/config/from/email'), Seven::getSiteConfig('mail/config/from/name'));
			parent::__construct();
		}
	
		public function getTemplate() {
			return $this->_template_id;
		}
		
		public function getTemplateVars() {
			return $this->_template_vars;
		}
		
		public function setTemplate($id, $vars = null) {
			if($vars !== null)
				$this->setTemplateVars($vars);
			$this->_template_id = $id;
			return $this;
		}
		
		public function setTemplateVars($vars) {
			$this->_template_vars = $vars ? (array)$vars : array();
			return $this;
		} 
		
		public function send($transport = null) {
			if($this->getTemplate()) {
				$tmpl = Seven::getModel('core/mail_template')->load($this->getTemplate());
				$body = $tmpl->getCompleteContent($this->getTemplateVars());
				if($tmpl->getType() == 'html') {
					$this->setBodyHtml($body);
				} else {
					$this->setBodyText($body);
				}
				$this->setSubject($tmpl->getCompleteSubject($this->getTemplateVars()));
			}
			if(Seven::getSiteConfig('mail/config/disable')) {
				Seven::log(E_NOTICE, 'Rejected e-mail to ' . implode(', ', $this->getRecipients()));
				return $this;
			}
			return parent::send($transport);
		}
	
	}
