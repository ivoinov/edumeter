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
 * @package    Core
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

class Core_Helper_Encrypt {

	public function encrypt($value, $method = 'md5') {
    	switch($method) {
    		case 'md5': 
    			return md5($value);
    		case 'md5+salt':
    			$pattern = "abcdef1234567890";
    			$rand_string = "";
    			$salt_length = mt_rand(2,4);
    			for($i = 0; $i < $salt_length; $i++)
    				$rand_string .= $pattern{mt_rand(0, strlen($pattern) - 1)};
    			return $rand_string . md5($rand_string . $value . $rand_string);
    		case 'md5+keys':
				$prefix = Seven::getConfig('crypt/prefix');
				$suffix = Seven::getConfig('crypt/suffix');
    			return ':' . md5($prefix . $value . $suffix);
    		case 'none':
    			return $value;
    		default:
    			throw new Exception('Incorrect encrypt method: ' . $method. ';');
    	}
    	return NULL;
	}
	
	public function campare($encoded, $value) {
		switch(true) {
			case $encoded && $encoded[0] == ':':
				$prefix = Seven::getConfig('crypt/prefix');
				$suffix = Seven::getConfig('crypt/suffix');
				return $encoded == ':' . md5($prefix . $value . $suffix);
			case strlen($encoded) == 32:             // MD5
				return $encoded == md5($value);
			case 34 <= strlen($encoded) && strlen($encoded) <= 36: // MD5 + salt
				$salt = substr($encoded, 0, strlen($encoded) - 32);
				$encrypted_value = substr($encoded, -32);
				return $encrypted_value == md5($salt . $value . $salt);
		}
		return $encoded == $value;
	}
	
}