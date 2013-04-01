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
 */


    class Seven_Maintenance {

	static function isEnabled() {
		if(!empty($_COOKIE['maintenance_code']) && !empty($_SERVER['SEVEN_MAINTENANCE_CODE'])
			&& $_COOKIE['maintenance_code'] == $_SERVER['SEVEN_MAINTENANCE_CODE'])
				return false;
		return ! empty($_SERVER['SEVEN_MAINTENANCE_ENABLE']);
	}

	static protected function registerToken() {
		if(!empty($_SERVER['SEVEN_MAINTENANCE_CODE']) && isset($_GET[$_SERVER['SEVEN_MAINTENANCE_CODE']]))
			setcookie("maintenance_code", $_COOKIE['maintenance_code'] = $_SERVER['SEVEN_MAINTENANCE_CODE'], 0, "/");
	}

	static function run($maintenance_template = null) {
		Seven_Maintenance::registerToken();
		if(Seven_Maintenance::isEnabled()) {
			if(!isset($_SERVER['SEVEN_MAINTENANCE_NOHEADER']) || !$_SERVER['SEVEN_MAINTENANCE_NOHEADER'])
				Header("HTTP/1.0 503 Service unavailable");

			if(!$maintenance_template && isset($_SERVER['SEVEN_MAINTENANCE_TEMPLATE']))
				$maintenance_template =  $_SERVER['SEVEN_MAINTENANCE_TEMPLATE'];

			if($maintenance_template)
			    require_once($maintenance_template);
			else
			    echo "<h1>Service temporary unavailable</h1>";

			exit();
		}
	}

    }