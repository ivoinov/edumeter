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
 */

require_once "../../app/seven.php";
Seven_Autoload::addPath(dirname(__FILE__) . DS . "inc");

$shell = new Shell();

$shell->put(array("Wellcome to Seven package creator!", "This script halp you create module for Seven Framework", ""));

$module = new System_Module(array('pool' => 'Seven', 'version' => '0.1.0', 'author' => function_exists('exec') ? exec('whoami') : NULL, 'is_active' => true, 'etc_path' => BP . DS . "shell" . DS . "developer" . DS . "etc" . DS . "addmodule" . DS));
do {
	do {
		
		$module->setName($shell->prompt("Enter module name", $module->getName()));
		$module->setPool($shell->prompt("Enter module pool", $module->getPool()));
		$module->setVersion($shell->prompt("Enter module version", $module->getVersion()));
		$module->setAuthor($shell->prompt("Enter module author", $module->getAuthor()));
		$module->setDescription($shell->prompt("Enter module description", $module->getDescription()));
		
		$module->setGenerateCrud($shell->confirm("Add crud controller", $module->getGenerateCrud()));
		
		if($module->getGenerateCrud()) {
			$module->setCrudRoute($shell->prompt("Enter CRUD controller route: ", $module->getCrudRoute()));
			$module->setCrudEntityName($shell->prompt("Enter entity name: ", $module->getCrudEntityName()));
		}
		
	} while(! $shell->confirm("Is data correct"));
	
	try {
		$module->create();
		$shell->put("Module successeffuly created!");
		break;
	} catch (Exception $e) {
		$shell->put("Error: " . $e->getMessage());
	}
} while($shell->confirm("Try again?"));

$shell->put("Bye bye!");