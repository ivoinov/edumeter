#!/usr/bin/php
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
 * @deprecated Since a lot of updates it need an upgrade
 */
     
    require_once "../../app/seven.php";
    Seven_Autoload::addPath(dirname(__FILE__) . DS . "inc");

    $shell = new Shell();
    
    $shell->put(array("Wellcome to Seven module delete script!",
		      "This script halp you delete module and all it data from Seven Framework",
		      ""
	));
	
    $module = new System_Module(array(
				    'etc_path'	=> BP . DS . "shell" . DS . "developer" . DS . "etc" . DS . "deletemodule" . DS,
				));
    try {
	$module->load($shell->prompt("Enter module name", $module->getName()));
	$depends = array();
	foreach(Seven::getConfig("modules") as $name => $desc) {
	    if($name != $module->getName() && !empty($desc['depends']) && in_array($module->getName(), array_keys((array)$desc['depends'])))
		$depends[$name] = $name;
	}
	if($depends)
	    $shell->put($module->getName() . " module required by: " . implode(" ", $depends));
	
	if($shell->confirm("Do you really want to delete module '" . $module->getName() . "' and all it data?")) {
	    $module->delete();
	    $shell->put("Module successeffuly deleted!");
	}
    } catch (Exception $e) {
        $shell->put("Error: " . $e->getMessage());
    }
    
    $shell->put("Bye bye!");