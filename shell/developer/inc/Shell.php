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
 * @package    Libs
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

    class Shell {
	    
		public function get($length = 1024) {
		    $stream = fopen("php://stdin", "r");
		    $line = fgets($stream, $length);
		    fclose($stream);
		    return $line;
		}
		
		public function put($message, $newline = true) {
		    if(is_array($message))
			$message = implode("\n", $message);
		    echo $message . ($newline ? "\n" : "");
		}
		
		public function confirm($message, $default = null) {
		    if($default === null) 
			$yn = "y/n";
		    else 
			$yn = ($default) ? "Y/n" : "y/N";
			
		    do {
			$this->put($message . " [" . $yn . "]? ", false);
			$input = trim(strtolower($this->get()));
		    } while(!($input == "y" || $input == "n" || ($input == "" && $default !== null)));
		    if($default !== null && !$input) $input = $default ? "y" : "n";
		    return $input == "y";
		}
		
		public function prompt($message, $default = NULL) {
		    $this->put($message . (($default === null) ? "" : " [$default]") . ": ", false);
		    $line = trim($this->get());
		    if(!$line && $default !== null)
			$line = $default;
		    if($line === "-")
			return "";
		    return $line;
		}
    
    }