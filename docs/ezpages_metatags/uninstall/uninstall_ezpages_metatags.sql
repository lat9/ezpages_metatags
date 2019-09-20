#
# Removes the fields previously added by the "EZ-Pages Meta-Tags" plugin.
#
ALTER TABLE ezpages_content
    DROP COLUMN pages_meta_title,
    DROP COLUMN pages_meta_keywords,
    DROP COLUMN pages_meta_description;
