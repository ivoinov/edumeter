<?php 

	abstract class Seven_Mail_Template_Processor_Abstract {
		
		abstract public function process($content, $vars = array(), $type = 'plain');
		
	}