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
// Only update configuration when an admin is logged in.
//
if (!isset($_SESSION['admin_id'])) {
    return;
}

// -----
// Continue checking, since there's a logged-in admin to view any messages.
//
// There's no 'configuration' associated with the plugin, just fields added to the database.  Continue
// processing only if the required fields aren't already present in the database.
//
if (!$sniffer->field_exists(TABLE_EZPAGES_CONTENT, 'pages_meta_title')) {
    $db->Execute(
        "ALTER TABLE " . TABLE_EZPAGES_CONTENT . " 
            ADD pages_meta_title VARCHAR(255) NOT NULL DEFAULT '', 
            ADD pages_meta_keywords text, 
            ADD pages_meta_description text"
    );
    
    // -----
    // Check to see if a previous (non-multi-lingual) version of the plugin was installed.  If not,
    // simply report the successful installation.
    //
    if (!$sniffer->field_exists(TABLE_EZPAGES, 'pages_meta_title')) {
        $messageStack->add_session(EZPAGES_METATAGS_INSTALLED, 'success');
    } else {
        // -----
        // Otherwise, a pre-v4.0.0 version of the plugin is installed.  We'll copy each page's
        // metatags definitions to **all** defined languages' in the ezpages_content table.
        //
        $epmt = $db->Execute(
            "SELECT pages_id, pages_meta_title, pages_meta_keywords, pages_meta_description
               FROM " . TABLE_EZPAGES
        );
        $languages = zen_get_languages();
        while (!$epmt->EOF) {
            $pages_id = $epmt->fields['pages_id'];
            $pages_meta_title = zen_db_input($epmt->fields['pages_meta_title']);
            $pages_meta_keywords = zen_db_input($epmt->fields['pages_meta_keywords']);
            $pages_meta_description = zen_db_input($epmt->fields['pages_meta_description']);
            for ($i = 0, $n = count($languages); $i < $n; $i++) {
                $db->Execute(
                    "UPDATE " . TABLE_EZPAGES_CONTENT . "
                        SET pages_meta_title = '$pages_meta_title',
                            pages_meta_keywords = '$pages_meta_keywords',
                            pages_meta_description = '$pages_meta_description'
                      WHERE pages_id = $pages_id
                        AND languages_id = " . $languages[$i]['id'] . "
                      LIMIT 1"
                );
            }
            $epmt->MoveNext();
        }
        
        // -----
        // Now that the content has been copied from the ezpages table, those fields are
        // removed from the 'base' ezpages table.
        //
        $db->Execute(
            "ALTER TABLE " . TABLE_EZPAGES . "
                DROP pages_meta_title,
                DROP pages_meta_keywords,
                DROP pages_meta_description"
        );
        
        // -----
        // Let the current admin know that the plugin has been updated.
        //
        $messageStack->add_session(EZPAGES_METATAGS_UPDATED, 'success');
    }
}
