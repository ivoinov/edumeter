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
 * @package    Access
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

$this->start();

$this->query("CREATE TABLE IF NOT EXISTS `access_role` (
  `role_id` INT( 8 ) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
");

$this->query("CREATE TABLE IF NOT EXISTS `access_role_permissions` (
  `role_id` int(8) DEFAULT NULL,
  `uri` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `access` enum('allow', 'deny') DEFAULT NULL,
  PRIMARY KEY (`role_id`,  `uri`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

$this->query("INSERT INTO  `access_role` (`role_id`, `name`) VALUES ('0',  'Guest');");
$this->query("UPDATE `access_role` SET  `role_id` =  '0' WHERE  `name` = 'Guest';");
$this->query("INSERT INTO  `access_role` (`role_id`, `name`) VALUES ('1',  'User');");
$this->query("INSERT INTO  `access_role` (`role_id`, `name`) VALUES ('2',  'Administrator');");
$this->query("ALTER TABLE  `access_role` AUTO_INCREMENT = 3");

$this->query("INSERT INTO  `access_role_permissions` (`role_id`, `uri`, `access`) VALUES ('0',  '/', 'allow');");
$this->query("INSERT INTO  `access_role_permissions` (`role_id`, `uri`, `access`) VALUES ('1',  '/', 'allow');");
$this->query("INSERT INTO  `access_role_permissions` (`role_id`, `uri`, `access`) VALUES ('2',  '/', 'allow');");

$this->query("ALTER TABLE  `users_user` ADD `role_id` int(8) NOT NULL DEFAULT '1'");

$this->end();