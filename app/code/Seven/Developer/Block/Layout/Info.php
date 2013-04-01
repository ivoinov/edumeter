<?php 

	class Developer_Block_Layout_Info extends Core_Block_Template {
		
		public function __construct($data = array()) {
			if(!isset($data['template']))
				$data['template'] = 'Seven/Developer/layout_info.phtml';
			return parent::__construct($data);
		}
		
		protected function _toHtml() {
			if(!Seven::getSiteConfig('webmaster/general/layout/debug'))
				return "";
			return parent::_toHtml();
		}
	}