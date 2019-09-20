<?php
// -----
// Part of the EZ-Pages Metatags plugin, v4.0.0+, provided by lat9
//
// Copyright (C) 2015-2019, Vinos de Frutas Tropicales
//
class EzPagesMetaTagsAdminObserver extends base 
{
    protected $pages_meta_title,
              $pages_meta_keywords,
              $pages_meta_description;
    function __construct() 
    {
        // -----
        // Watch for various notifications from /admin/ezpages.php, _only_ when
        // that module is the "current_page" and the EZ-Pages' meta-tags fields
        // have been added to the ezpages_content table.
        //
        if ($GLOBALS['current_page'] == (FILENAME_EZPAGES_ADMIN . '.php') && $GLOBALS['sniffer']->field_exists(TABLE_EZPAGES_CONTENT, 'pages_meta_title')) {
            $this->attach(
                $this, 
                array(
                    'NOTIFY_ADMIN_EZPAGES_UPDATE_BASE',
                    'NOTIFY_ADMIN_EZPAGES_UPDATE_LANG_INSERT',
                    'NOTIFY_ADMIN_EZPAGES_UPDATE_LANG_UPDATE',
                    'NOTIFY_ADMIN_EZPAGES_NEW',
                    'NOTIFY_ADMIN_EZPAGES_FORM_FIELDS',
                    'NOTIFY_ADMIN_EZPAGES_EXTRA_ACTION_ICONS',
                )
            );
        }
    }
  
    function update (&$class, $eventID, $p1, &$p2, &$p3, &$p4, &$p5) 
    {
        switch ($eventID) {
            // -----
            // Preparing to perform an update/insert of an EZ-page's content.  We'll record
            // a sanitized version of the meta-tag information for use on subsequent notifications
            // of the language-based fields.  On entry:
            //
            // $p1 ... (r/o) The 'action' being performed, one of 'insert' or 'update'.
            // $p2 ... (r/w) A reference to the boolean $page_error value, which indicates whether/not a
            //               data-related issue has been found.  Not used by this processing.
            // $p3 ... (r/w) A reference to the $sql_data_array containing the 'ezpages' table data to
            //               be recorded if no error has occurred.
            //
            case 'NOTIFY_ADMIN_EZPAGES_UPDATE_BASE':
                $this->pages_meta_title = zen_db_prepare_input($_POST['pages_meta_title']);
                $this->pages_meta_keywords = zen_db_prepare_input($_POST['pages_meta_keywords']);
                $this->pages_meta_description = zen_db_prepare_input($_POST['pages_meta_description']);
                break;

            // -----
            // Preparing to insert a language-specific record associated with a just-inserted
            // ezpage.  On entry:
            //
            // $p1 ... (r/o) An associative array containing the 'pages_id' and 'language_id' for the insert.
            // $p2 ... (r/w) A reference to the to-be-written, language-specific ezpages_content record.
            //
            case 'NOTIFY_ADMIN_EZPAGES_UPDATE_LANG_INSERT':
                $p2 = $this->addEzPagesMetaTagsFields($p2, $p1['pages_id'], $p1['languages_id']);
                break;

            // -----
            // Preparing to update a language-specific record associated with an existing ezpage.
            // On entry:
            //
            // $p1 ... (r/o) An associative array containing the 'pages_id' and 'language_id' for the update.
            // $p2 ... (r/w) A reference to the to-be-written, language-specific ezpages_content record.
            //
            case 'NOTIFY_ADMIN_EZPAGES_UPDATE_LANG_UPDATE':
                $p2 = $this->addEzPagesMetaTagsFields($p2, $p1['pages_id'], $p1['languages_id']);
                break;

            // -----
            // Preparing to create a new ezpage, provide defaults for the meta-tag fields.
            //
            // $p2 ... (r/w) A reference to the $parameters array, to which the meta-tag fields' default values are inserted.
            //
            case 'NOTIFY_ADMIN_EZPAGES_NEW':
                $p2['pages_meta_title'] = '';
                $p2['pages_meta_keywords'] = '';
                $p2['pages_meta_description'] = '';
                break;

            // -----
            // Preparing the form through which an ezpage's data is initially entered or updated.  On entry:
            //
            // $p1 ... (r/o) A copy of the current $ezInfo object, containing the values to be initially displayed.
            // $p2 ... (r/w) A reference to the $extra_page_inputs array, to which we'll add the input fields for
            //               the various meta-tags elements. Each entry in that array contains an array of the following format:
            //
            //               array(
            //                  'label' => array(
            //                      'text' => 'The label text',   (required)
            //                      'field_name' => 'The name of the field associated with the label', (required)
            //                      'addl_class' => {Any additional class to be applied to the label} (optional)
            //                      'parms' => {Any additional parameters for the label, e.g. 'style="font-weight: 700;"} (optional)
            //                  ),
            //                  'input' => 'The HTML to be inserted' (required)
            //               )
            //
            case 'NOTIFY_ADMIN_EZPAGES_FORM_FIELDS':
                $pages_id = (!empty($_GET['ezID'])) ? (int)$_GET['ezID'] : 0;
                $p2[] = $this->createEzPageMetaTagTitleInput($pages_id);
                $p2[] = $this->createEzPageMetaTagKeywordsInput($pages_id);
                $p2[] = $this->createEzPageMetaTagDescriptionInput($pages_id);
                break;

            // -----
            // Building the listing of all EZ-Pages information, allows us to insert an indicator as to whether or
            // not meta-tags are active for the current ezpage (it's issued once per defined page).  On entry:
            //
            // $p1 ... (r/o) Contains the database information array for the page-line currently being rendered.
            // $p2 ... (r/w) A reference to the $extra_icons string, to which we'll append the current page's meta-tags' status.
            //               Note that the status shown will be for the active language only!
            //
            case 'NOTIFY_ADMIN_EZPAGES_EXTRA_ACTION_ICONS':
                $pages_id = $p1['pages_id'];
                $check = $GLOBALS['db']->Execute(
                    "SELECT pages_meta_title, pages_meta_keywords, pages_meta_description
                       FROM " . TABLE_EZPAGES_CONTENT . "
                      WHERE pages_id = $pages_id
                        AND languages_id = " . (int)$_SESSION['languages_id'] . "
                      LIMIT 1"
                );
                $epmt_onoff = ($check->EOF || empty($check->fields['pages_meta_title'] . $check->fields['pages_meta_keywords'] . $check->fields['pages_meta_description'])) ? 'off' : 'on';
                $pages_link = zen_href_link(FILENAME_EZPAGES_ADMIN, (isset($_GET['page']) ? 'page=' . (int)$_GET['page'] . '&amp;' : '') . "ezID=$pages_id&amp;action=new");
                $p2 .= sprintf(EZPAGES_METATAGS_ICON, $pages_link, $epmt_onoff);
                break;

            default:
                break;
        }
    }
    
    // -----
    // Protected method to add the EZ-Page's meta-tags to any insert/update action.
    //
    protected function addEzPagesMetaTagsFields($sql_data_array, $pages_id, $language_id)
    {
        $sql_data_array['pages_meta_title'] = $this->pages_meta_title[$language_id];
        $sql_data_array['pages_meta_keywords'] = $this->pages_meta_keywords[$language_id];
        $sql_data_array['pages_meta_description'] = $this->pages_meta_description[$language_id];
        return $sql_data_array;
    }
    
    // -----
    // Protected method to create the input area for the current EZ-page's metatag title.
    //
    protected function createEzPageMetaTagTitleInput($pages_id)
    {
        $languages = $GLOBALS['languages'];
        $meta_inputs = '';
        foreach ($languages as $language) {
            $page_info = $GLOBALS['db']->Execute(
                "SELECT *
                   FROM " . TABLE_EZPAGES_CONTENT . "
                  WHERE pages_id = $pages_id
                    AND languages_id = {$language['id']}
                  LIMIT 1"
            );
            $pages_meta_title = (!$page_info->EOF) ? $page_info->fields['pages_meta_title'] : '';
            $meta_inputs .= '<div class="input-group">';
            $meta_inputs .= '<span class="input-group-addon">' . zen_image(DIR_WS_CATALOG_LANGUAGES . $language['directory'] . '/images/' . $language['image'], $language['name']) . '</span>';
            $meta_inputs .= zen_draw_input_field('pages_meta_title[' . $language['id'] . ']', htmlspecialchars($pages_meta_title, ENT_COMPAT, CHARSET, TRUE), zen_set_field_length(TABLE_EZPAGES_CONTENT, 'pages_meta_title') . ' class="form-control"', true);
            $meta_inputs .= '</div><br>';
        }
        return array(
            'label' => array(
                'text' => EZPAGES_METATAGS_TITLE_LABEL,
                'field_name' => 'pages_meta_title'
            ),
            'input' => $meta_inputs,
        );
    }
    
    // -----
    // Protected method to create the input area for the current EZ-page's metatag keywords.
    //
    protected function createEzPageMetaTagKeywordsInput($pages_id)
    {
        $languages = $GLOBALS['languages'];
        $meta_inputs = '';
        foreach ($languages as $language) {
            $page_info = $GLOBALS['db']->Execute(
                "SELECT *
                   FROM " . TABLE_EZPAGES_CONTENT . "
                  WHERE pages_id = $pages_id
                    AND languages_id = {$language['id']}
                  LIMIT 1"
            );
            $pages_meta_keywords = (!$page_info->EOF) ? $page_info->fields['pages_meta_keywords'] : '';
            $meta_inputs .= '<div class="input-group">';
            $meta_inputs .= '<span class="input-group-addon">' . zen_image(DIR_WS_CATALOG_LANGUAGES . $language['directory'] . '/images/' . $language['image'], $language['name']) . '</span>';
            $meta_inputs .= zen_draw_textarea_field('pages_meta_keywords[' . $language['id'] . ']', 'soft', '100%', '3', htmlspecialchars($pages_meta_keywords, ENT_COMPAT, CHARSET, TRUE), 'class="noEditor form-control"');
            $meta_inputs .= '</div><br>';
        }
        return array(
            'label' => array(
                'text' => EZPAGES_METATAGS_KEYWORDS_LABEL,
                'field_name' => 'pages_meta_keywords'
            ),
            'input' => $meta_inputs,
        );
    }
    
    // -----
    // Protected method to create the input area for the current EZ-page's metatag description.
    //
    protected function createEzPageMetaTagDescriptionInput($pages_id)
    {
        $languages = $GLOBALS['languages'];
        $meta_inputs = '';
        foreach ($languages as $language) {
            $page_info = $GLOBALS['db']->Execute(
                "SELECT *
                   FROM " . TABLE_EZPAGES_CONTENT . "
                  WHERE pages_id = $pages_id
                    AND languages_id = {$language['id']}
                  LIMIT 1"
            );
            $pages_meta_description = (!$page_info->EOF) ? $page_info->fields['pages_meta_description'] : '';
            $meta_inputs .= '<div class="input-group">';
            $meta_inputs .= '<span class="input-group-addon">' . zen_image(DIR_WS_CATALOG_LANGUAGES . $language['directory'] . '/images/' . $language['image'], $language['name']) . '</span>';
            $meta_inputs .= zen_draw_textarea_field('pages_meta_description[' . $language['id'] . ']', 'soft', '100%', '3', htmlspecialchars($pages_meta_description, ENT_COMPAT, CHARSET, TRUE), 'class="noEditor form-control"');
            $meta_inputs .= '</div><br>';
        }
        return array(
            'label' => array(
                'text' => EZPAGES_METATAGS_DESC_LABEL,
                'field_name' => 'pages_meta_description'
            ),
            'input' => $meta_inputs,
        );
    }
}
