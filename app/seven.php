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
 * @package    Seven
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * Bootstrap file
 *
 */
	
	define("DS", DIRECTORY_SEPARATOR);
	define("PS", PATH_SEPARATOR);
	define("BP", dirname(dirname(__FILE__)));
	define("MP", 'public'.DS.'media');
	
	umask(0002);
	
	include BP . DS . "lib" . DS . "Seven" . DS . "Autoload.php";
	
	Seven_Autoload::addPath(BP);
	Seven_Autoload::addPath(BP . DS . "lib");
	Seven_Autoload::addPath(BP . DS . "app" . DS . "code");
	Seven_Autoload::addPath(BP . DS . "app" . DS . "code" . DS . "Seven");
	Seven_Autoload::register();
	
	include BP . DS . "app" . DS . "code" . DS . "Seven" . DS . "functions.php";
	
	chdir(BP);
	
	set_error_handler(function($errno, $errstr) {
                if(!(error_reporting() & $errno))
                    return 0;
		if($errno == E_NOTICE)
			throw new Core_Exception_Notice($errstr, $errno);
		if($errno == E_WARNING)
			throw new Core_Exception_Warning($errstr, $errno);
		if($errno == E_ERROR)
			throw new Core_Exception_Error($errstr, $errno);
		throw new Exception($errstr, $errno);
	});

//  register_shutdown_function(function() { if($error = error_get_last()) throw new Exception($error['message']); });
