<?php
// -----
// Part of the Meta Tags for EZ-Pages plugin.  Copyright 2015, Vinos de Frutas Tropicales (http://vinosdefrutastropicales.com).
//
$db->Execute ("CREATE TABLE IF NOT EXISTS " . TABLE_EZPAGES_METATAGS . " (
  `pages_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL default '1',
  `metatags_title` varchar(255) NOT NULL default '',
  `metatags_keywords` text,
  `metatags_description` text,
  PRIMARY KEY  (`pages_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=" . DB_CHARSET);