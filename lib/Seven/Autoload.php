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
 *
 */

    class Seven_Autoload {

        static public function addPath($path) {
            $paths = explode(PATH_SEPARATOR, get_include_path());
            if(in_array($path, $paths)) return;
            $paths[] = $path;
            set_include_path(implode(PATH_SEPARATOR, $paths));
        }

        static public function register() {
            spl_autoload_register(__CLASS__ . "::load");
        }

        static public function load($class_name) {
            $class_path = str_replace("_", DIRECTORY_SEPARATOR, $class_name) . ".php";
            try {
        	return include_once $class_path;
    	    } catch(Exception $e) {
    		if($e->getCode() != 2) // 2 it's a not exists file error
    		    throw $e;
    		return false;
    	    }
        }

    }
