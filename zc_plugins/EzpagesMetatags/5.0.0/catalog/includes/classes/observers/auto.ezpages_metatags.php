<?php
class zcObserverEzpagesMetatags extends base
{
    public function __construct()
    {
        $this->attach($this, [
            'NOTIFY_MODULE_META_TAGS_UNSPECIFIEDPAGE',
        ]);
    }

    public function updateNotifyModuleMetaTagsUnspecifiedPage(&$class, $eventID, $p1, &$p2, &$p3, &$p4, &$p5, &$p6)
    {
        $pages_id = (int)($_GET['id'] ?? 0);
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
    }
}
