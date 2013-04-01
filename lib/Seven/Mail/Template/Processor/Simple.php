<?php 

	class Seven_Mail_Template_Processor_Simple extends Seven_Mail_Template_Processor_Abstract {
		
		public function process($content, $vars = array(), $type = 'plain') {
			return preg_replace_callback("/\{\{([a-z][a-z_0-9\.]*)\}\}/i", function($match) use($vars) {
				$key = $match[1];
				if(isset($vars[$key]))
					return $vars[$key];
				return $match[0];
			}, $content);
		}
		
	}