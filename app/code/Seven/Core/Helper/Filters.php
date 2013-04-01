<?php 

	class Core_Helper_Filters {
		
		public function trim($string, $input) {
			return trim($string);
		}
		
		public function strip_tags($string, $input) {
			$allowable_tags = $input->getAllowedTags() ? ('<' . implode('><', array_filter(array_map('trim', explode(",", $input->getAllowedTags())))) . '>') : NULL;
			return strip_tags($string, $allowable_tags);
		}
		
		public function trimlines($text, $input) {
			$lines = array_map('trim', explode("\n", $text));
			if($input->getTrimLineFilter() === null || $input->getTrimLineFilter()) 
				$lines = array_filter($lines);
			return implode("\n", $lines);
		}
		
	}