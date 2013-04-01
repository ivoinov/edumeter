#!/usr/bin/env php
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

require "../app/seven.php";

try {
	foreach(Seven::getCollection('core/package')->getActive()->load() as $package) {
	    $version = $package->getInstalledVersion();
	    $package->upgrade();
	    if($version != $package->getInstalledVersion())
		echo $package->getName() . " upgraded from {$version} to " . $package->getInstalledVersion() . "\n";
	}
} catch(Exception $e) {
	echo $e;
}
