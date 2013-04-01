<?php 

$this->query('
CREATE TABLE `admin_user` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `email` VARCHAR(255) NOT NULL,
      `password` VARCHAR(64) NOT NULL,
      `name` VARCHAR(255) NOT NULL,
      PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;');

$this->query('INSERT INTO `admin_user` VALUES(NULL, ?, ?, ?)', array(
	"john@sevenframework.com",
	md5("abc123"),
	"John Doe"
));