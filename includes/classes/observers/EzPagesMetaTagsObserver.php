<?php
// -----
// Part of the EZ-Pages Metatags plugin, v4.0.0+, provided by lat9
//
// Copyright (C) 2015-2019, Vinos de Frutas Tropicales
//
class EzPagesMetaTagsObserver extends base 
{
    function __construct() 
    {
        // -----
        // Watch for meta-tags' insertion event, if the meta-tags' fields are
        // present in the database.
        //
        if ($GLOBALS['current_page_base'] == FILENAME_EZPAGES && $GLOBALS['sniffer']->field_exists(TABLE_EZPAGES_CONTENT, 'pages_meta_title')) {
            $this->attach(
                $this, 
                array(
                    'NOTIFY_MODULE_META_TAGS_UNSPECIFIEDPAGE', 
                )
            );
        }
    }
  

    function update (&$class, $eventID, $p1, &$p2, &$p3, &$p4, &$p5, &$p6) 
    {
        switch ($eventID) {
            // -----
            // Watching for the metatags' generation event (only when main_page=ezpages).  On entry:
            //
            // $p3 ... (r/w) A reference to the boolean override flag.  Set to (bool)true if this processing has
            //               overridden the meta-tags' information.
            // $p4 ... (r/w) A reference to the current value of the $metatags_title, possibly overwritten by this processing.
            // $p5 ... (r/w) A reference to the current value of the $metatags_description, possibly overwritten by this processing.
            // $p6 ... (r/w) A reference to the current value of the $metatags_keywords, possibly overwritten by this processing.
            //
            case 'NOTIFY_MODULE_META_TAGS_UNSPECIFIEDPAGE':
                // -----
                // Quick return if either no Ez-pages id is specified, the value's out of range or the metatags'
                // information has already been overridden..
                //
                $pages_id = (isset($_GET['id'])) ? (int)$_GET['id'] : 0;
                if ($p3 !== false || $pages_id <= 0) {
                    return;
                }
                
                // -----
                // Retrieve the metatags information associated with the current EZ-Page.  If that query
                // returns no results, the associated EZ-page doesn't exist, so we'll bail.
                //
                $eztags = $GLOBALS['db']->Execute(
                    "SELECT *
                       FROM " . TABLE_EZPAGES_CONTENT . "
                      WHERE pages_id = $pages_id
                        AND languages_id = " . (int)$_SESSION['languages_id'] . "
                      LIMIT 1"
                );
                if ($eztags->EOF) {
                    return;
                }
                
                // -----
                // Each metatag element's value "might" be overridden ... if both of the following cases are valid:
                //
                // 1) The associated definition doesn't already exist; it might if a language-file-based
                //    override is in effect.
                // 2) The value recorded in the database is not an "empty" value.
                //
                if (!defined('META_TAG_TITLE') && !empty($eztags->fields['pages_meta_title'])) {
                    $p4 = zen_clean_html($eztags->fields['pages_meta_title']);
                    $p3 = true;
                }
                if (!defined('META_TAG_DESCRIPTION') && !empty($eztags->fields['pages_meta_description'])) {
                    $p5 = zen_clean_html($eztags->fields['pages_meta_description']);
                    $p3 = true;
                }
                if (!defined('META_TAG_KEYWORDS') && !empty($eztags->fields['pages_meta_keywords'])) {
                    $p6 = zen_clean_html($eztags->fields['pages_meta_keywords']);
                    $p3 = true;
                }
                break;

            default:
                break;
        }
    }
}
