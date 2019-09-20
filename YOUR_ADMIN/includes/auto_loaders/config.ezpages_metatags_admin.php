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
// Load the plugin's database initialization script.
//
$autoLoadConfig[200][] = array(
    'autoType' => 'init_script',
    'loadFile' => 'init_ezpages_metatags_admin.php'
);

// -----
// Load the plugin's observer-class, watching for events notified from
// /admin/ezpages.php.
//
$autoLoadConfig[200][] = array(
    'autoType' => 'class',
    'loadFile' => 'observers/EzPagesMetaTagsAdminObserver.php',
    'classPath' => DIR_WS_CLASSES
);
$autoLoadConfig[200][] = array(
    'autoType' => 'classInstantiate',
    'className' => 'EzPagesMetaTagsAdminObserver',
    'objectName' => 'epmt'
);
