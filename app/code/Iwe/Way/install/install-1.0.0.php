<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ivoinov
 * Date: 5/5/13
 * Time: 12:30 PM
 * To change this template use File | Settings | File Templates.
 */
$this->start();

$this->query("CREATE TABLE IF NOT EXISTS `iwe_way_entity` (
`id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `subjects` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2;");

$this->end();
