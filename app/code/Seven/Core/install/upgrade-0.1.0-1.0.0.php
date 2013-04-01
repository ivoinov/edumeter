<?php 

	$this->query("CREATE TABLE `core_mail_template` (
      `id` VARCHAR(255) NOT NULL,
      `subject` VARCHAR(255) NOT NULL,
      `content` TEXT NOT NULL,
      `type` VARCHAR(255) NOT NULL,
      `_view_id` VARCHAR(255) NOT NULL,
      PRIMARY KEY (`id`, `_view_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;");