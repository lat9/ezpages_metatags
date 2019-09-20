<?php
// -----
// Part of the EZ-Pages Metatags plugin, v4.0.0+, provided by lat9
//
// Copyright (C) 2015-2019, Vinos de Frutas Tropicales
//
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

// -----
// Load towards the end; the meta-tags' processing is brought in by the site's
// html_header.php.
//
$autoLoadConfig[200][] = array(
    'autoType' => 'class',
    'loadFile' => 'observers/EzPagesMetaTagsObserver.php'
);
$autoLoadConfig[200][] = array(
    'autoType' => 'classInstantiate',
    'className' => 'EzPagesMetaTagsObserver',
    'objectName' => 'epmt'
);
