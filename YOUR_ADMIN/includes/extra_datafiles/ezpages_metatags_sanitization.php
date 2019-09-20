<?php
// -----
// Part of the EZ-Pages Metatags plugin, v4.0.0+, provided by lat9
//
// Copyright (C) 2019, Vinos de Frutas Tropicales
//
$epmt_sanitizer = AdminRequestSanitizer::getInstance();

// -----
// Instruct the admin sanitizer to 'sanitize' the EZ-Pages' metatags values in a manner similar
// to those for the products.
//
$epmt_sanitizer->addSimpleSanitization('META_TAGS', array('pages_meta_title', 'pages_meta_keywords', 'pages_meta_description'));
