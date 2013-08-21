CREATE TABLE IF NOT EXISTS `blog_hubs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;



CREATE TABLE IF NOT EXISTS `forum_topics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `blog_posts` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`blog_id` int(10) not null, default '0',
	`title`  varchar(255) NOT NULL,
	`anons` TEXT NOT NULL,
	`full_text` TEXT NOT NULL,
	`vote_down` int(10) not null default '0',
	`votes` int(10) not null default '0',
	`rating` float(14,10) NOT NULL DEFAULT '0.0000000000',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;



CREATE TABLE IF NOT EXISTS `blogs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` TEXT NULL,
  `rating` float(14,10) NOT NULL DEFAULT '0.0000000000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;





CREATE TABLE IF NOT EXISTS `block` (
  `id_group` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `tabs` mediumtext NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;



-- Страницы --



CREATE TABLE IF NOT EXISTS `contents` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `code` varchar(255) NOT NULL,
 `parent_id` int(10)  NOT NULL default '0',
 PRIMARY KEY (`id`),
 KEY `idx_parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `content_pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` int(10) NOT NULL default '0',
  `rcount` int(10) NOT NULL default '0',
  `lang_id` tinyint(3) NOT NULL default '0',
  `title` varchar(255) NULL,
  `keywords` varchar(255) NULL,
  `description` varchar(255) NULL,
  `content` TEXT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_content_id` (`content_id`),
  KEY `idx_content_lang_id` (`content_id`,`lang_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `lang` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `code` varchar(2) NOT NULL DEFAULT '',
  `name` varchar(62) NOT NULL,
  `default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `lang` (`id`, `code`, `name`, `default`) VALUES
(1, 'ru', 'Русский', 1);

CREATE TABLE IF NOT EXISTS `session_storage` (
  `sid` char(62) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `expires` int(18) NOT NULL,
  `session_data` longtext NOT NULL,
  PRIMARY KEY (`sid`),
  KEY `expires` (`expires`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- Страницы --
