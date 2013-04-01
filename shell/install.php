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

    require_once "../app/seven.php";

    class Install_Config extends Core_Model_Config {

        protected function _preloadConfig() {
            if($this->_preloaded) return;
            $this->_preloaded = true;
            $this->_preloadBaseConfig();
        }

        public function getOption($key, $area = null) {
            return parent::getOption($key, 'system');
        }

    }

    $config = new Install_Config();
    $adapter = Seven_Db::factory($config->getOption('resources/db/default/type'), $config->getOption('resources/db/default'));


    try {
        $adapter->query("CREATE TABLE `core_locales` (`code` char(5) NOT NULL, `language` char(5) NOT NULL, `name` varchar(255) NOT NULL, UNIQUE KEY `code` (`code`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $adapter->query("INSERT INTO  `core_locales` (`code`, `language`, `name`) VALUES('en-US', 'en-US', 'English');");
        $adapter->query("CREATE TABLE `core_package` ( `id` varchar(64) NOT NULL, `availabel` int(1) NOT NULL, `version` varchar(10) DEFAULT NULL, `name` varchar(64) NOT NULL, `installed_version` varchar(10) DEFAULT NULL, `description` varchar(255) DEFAULT NULL, `pool` varchar(32) NOT NULL, `author` varchar(64) DEFAULT NULL, `depends` text, `active` int(1) DEFAULT NULL, `index` int(11) NOT NULL DEFAULT 0, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        $adapter->query("CREATE TABLE `core_website` ( `id` int(11) NOT NULL AUTO_INCREMENT,  `name` varchar(255) NOT NULL, `code` varchar(32) NOT NULL, `url` varchar(255) NOT NULL, `area` varchar(16) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;");
        $adapter->query("INSERT INTO  `core_website` (`id`, `name`, `code`, `url`, `area`) VALUES (1, 'Admin', 'admin', '', 'admin'), (2, 'Frontend', '', '', 'frontend');");
        $adapter->query("CREATE TABLE `core_website_config` ( `option_id` int(11) NOT NULL AUTO_INCREMENT, `path` varchar(255) DEFAULT NULL, `value` text, `scope` varchar(11) NOT NULL DEFAULT 'global', PRIMARY KEY (`option_id`), UNIQUE KEY `path` (`path`,`scope`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8;");
    } catch(Exception $e) {
        echo $e;
    }
