<?php

	class Core_Model_Mail_Template extends Core_Model_Multiview_Entity {
		
		protected $_alias = 'core/mail_template';
		
		/**
		 * @return Seven_Mail_Template_Processor_Abstract
		 */
		
		protected function _getTemplateProcessor() {
			return Seven::getSingleton(Seven::getConfig('mail/processor') ? : 'Seven_Mail_Template_Processor_Simple');
		}
	
		public function getCompleteContent($data) {
			return $this->_getTemplateProcessor()->process($this->getContent(), $data, $this->getType());
		}
		
		public function getCompleteSubject($data) {
			return $this->_getTemplateProcessor()->process($this->getSubject(), $data, 'plain');
		}
		
	}