<?php
// -----
// Part of the EZ-Pages Metatags plugin, v4.0.0+, provided by lat9
//
// Copyright (C) 2015-2019, Vinos de Frutas Tropicales
//
//- Used for the labels associated with the ezpages' data-entry screen.
define('EZPAGES_METATAGS_TITLE_LABEL', 'Meta Title:');
define('EZPAGES_METATAGS_KEYWORDS_LABEL', 'Meta Keywords:');
define('EZPAGES_METATAGS_DESC_LABEL', 'Meta Description:');

// -----
// Used to form the metatags' icon-link on the EZ-Pages listing.
//
// %1$s ... Filled in with the link to the current EZ-Page.
// %2$s ... Filled in with either 'on' or 'off', depending on the page's metatags status.
//
define('EZPAGES_METATAGS_ICON', 
    '<a href="%1$s" style="text-decoration: none"><div class="fa-stack fa-lg metatags-%2$s"><i class="fa fa-circle fa-stack-2x base"></i><i class="fa fa-asterisk fa-stack-1x overlay" aria-hidden="true"></i></div></a>');


// -----
// Installation/update messages.
//
define('EZPAGES_METATAGS_INSTALLED', '<em>EZ-Pages Metatags</em>, v4.0.0, was successfully installed.');
define('EZPAGES_METATAGS_UPDATED', '<em>EZ-Pages Metatags</em> was successfully updated to v4.0.0.');
