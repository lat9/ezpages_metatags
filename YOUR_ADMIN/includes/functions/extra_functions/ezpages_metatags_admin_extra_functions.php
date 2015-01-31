<?php
// -----
// Part of the Meta Tags for EZ-Pages plugin.  Copyright 2015, Vinos de Frutas Tropicales (http://vinosdefrutastropicales.com).
//
if (!$sniffer->field_exists (TABLE_EZPAGES, 'pages_meta_title')) {
  $db->Execute ("ALTER TABLE " . TABLE_EZPAGES . " ADD pages_meta_title VARCHAR(255) NOT NULL DEFAULT '', ADD pages_meta_keywords text, ADD pages_meta_description text");
  
}

function get_ezpage_metatags_icon ($pages_id) {
  global $db;
  $ezpages_metatags = $db->Execute ("SELECT pages_meta_title, pages_meta_keywords, pages_meta_description FROM " . TABLE_EZPAGES . " WHERE pages_id = " . (int)$pages_id);
  $metatag_icon = 'icon_edit_metatags_off.gif';
  $metatag_icon_alt = ICON_METATAGS_OFF;
  while (!$ezpages_metatags->EOF) {
    if (zen_not_null ($ezpages_metatags->fields['pages_meta_title']) || zen_not_null ($ezpages_metatags->fields['pages_meta_keywords']) || zen_not_null ($ezpages_metatags->fields['pages_meta_description'])) {
      $metatag_icon = 'icon_edit_metatags_on.gif';
      $metatag_icon_alt = ICON_METATAGS_ON;
      break;
      
    }
    $ezpages_metatags->MoveNext ();
    
  }
  return zen_image (DIR_WS_IMAGES . $metatag_icon, $metatag_icon_alt);

}