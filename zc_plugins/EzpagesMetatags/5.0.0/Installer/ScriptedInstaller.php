<?php
use Zencart\PluginSupport\ScriptedInstaller as ScriptedInstallBase;

class ScriptedInstaller extends ScriptedInstallBase
{
    protected function executeInstall()
    {
        if ($this->removePrepluginVersion() === false) {
            return false;
        }
        if ($this->tableCheckup() === false) {
            return false;
        }

        parent::executeInstall();
        return true;
    }

    protected function tableCheckup(): bool
    {
        global $sniffer;

        if (!$sniffer->field_exists(TABLE_EZPAGES_CONTENT, 'pages_meta_title')) {
            $this->executeInstallerSql(
                "ALTER TABLE " . TABLE_EZPAGES_CONTENT . "
                    ADD pages_meta_title VARCHAR(255) NOT NULL DEFAULT '',
                    ADD pages_meta_keywords text,
                    ADD pages_meta_description text"
            );
        }

        // -----
        // Check to see if a previous (non-multi-lingual) version of the plugin was installed.  If so,
        // a pre-v4.0.0 version of the plugin is installed.  We'll copy each page's
        // metatags definitions to **all** defined languages' in the ezpages_content table.
        //
        if ($sniffer->field_exists(TABLE_EZPAGES, 'pages_meta_title')) {
            $epmt = $this->executeInstallerSelectQuery(
                "SELECT pages_id, pages_meta_title, pages_meta_keywords, pages_meta_description
                   FROM " . TABLE_EZPAGES
            );
            if ($epmt === false) {
                return false;
            }

            $languages = zen_get_languages();
            foreach ($epmt as $next_rec) {
                $pages_id = $next_rec['pages_id'];
                $pages_meta_title = zen_db_input($next_rec['pages_meta_title']);
                $pages_meta_keywords = zen_db_input($next_rec['pages_meta_keywords']);
                $pages_meta_description = zen_db_input($next_rec['pages_meta_description']);
                foreach ($languages as $next_lang) {
                    $this->executeInstallerSql(
                        "UPDATE " . TABLE_EZPAGES_CONTENT . "
                            SET pages_meta_title = '$pages_meta_title',
                                pages_meta_keywords = '$pages_meta_keywords',
                                pages_meta_description = '$pages_meta_description'
                          WHERE pages_id = $pages_id
                            AND languages_id = " . $next_lang['id'] . "
                          LIMIT 1"
                    );
                }
            }

            // -----
            // Now that the content has been copied from the ezpages table, those fields are
            // removed from the 'base' ezpages table.
            //
            $this->executeInstallerSql(
                "ALTER TABLE " . TABLE_EZPAGES . "
                    DROP pages_meta_title,
                    DROP pages_meta_keywords,
                    DROP pages_meta_description"
            );
        }

        return true;
    }

    protected function removePrepluginVersion(): bool
    {
        // -----
        // First, look for and remove the non-encapsulated versions' admin-directory
        // file.
        //
        $files_to_check = [
            'includes/auto_loaders/' => [
                'config.ezpages_metatags_admin.php',
            ],
            'includes/classes/observers/' => [
                'EzPagesMetaTagsAdminObserver.php',
            ],
            'includes/extra_datafiles/' => [
                'ezpages_metatags_sanitization.php',
            ],
            'includes/functions/extra_functions/' => [
                'ezpages_metatags_admin_extra_functions.php',
            ],
            'includes/init_includes/' => [
                'init_ezpages_metatags_admin.php',
            ],
            'includes/languages/english/extra_definitions/' => [
                'ezpages_metatags_extra_definitions.php',
            ],
        ];

        $errorOccurred = false;
        foreach ($files_to_check as $dir => $files) {
            $current_dir = DIR_FS_ADMIN . $dir;
            foreach ($files as $next_file) {
                $current_file = $current_dir . $next_file;
                if (file_exists($current_file)) {
                    unlink($current_file);
                    if (file_exists($current_file)) {
                        $errorOccurred = true;
                        $this->errorContainer->addError(
                            0,
                            sprintf(ERROR_UNABLE_TO_DELETE_FILE, $current_file),
                            false,
                            // this str_replace has to do DIR_FS_ADMIN before CATALOG because catalog is contained within admin, so results are wrong.
                            // also, '[admin_directory]' is used to obfuscate the admin dir name, in case the user copy/pastes output to a public forum for help.
                            sprintf(ERROR_UNABLE_TO_DELETE_FILE, str_replace([DIR_FS_ADMIN, DIR_FS_CATALOG], ['[admin_directory]/', ''], $current_file))
                        );
                    }
                }
            }
        }

        // -----
        // Next, locate and attempt to remove the storefront files.
        //
        $files_to_check = [
            'includes/auto_loaders' => [
                'config.ezpages_metatags.php',
            ],
            'includes/classes/observers/' => [
                'EzPagesMetaTagsObserver.php',
            ],
        ];
        foreach ($files_to_check as $dir => $files) {
            $current_dir = DIR_FS_CATALOG . $dir;
            foreach ($files as $next_file) {
                $current_file = $current_dir . $next_file;
                 if (file_exists($current_file)) {
                    unlink($current_file);
                    if (file_exists($current_file)) {
                        $errorOccurred = true;
                        $this->errorContainer->addError(
                            0,
                            sprintf(ERROR_UNABLE_TO_DELETE_FILE, $current_file),
                            false,
                            // this str_replace has to do DIR_FS_ADMIN before CATALOG because catalog is contained within admin, so results are wrong.
                            // also, '[admin_directory]' is used to obfuscate the admin dir name, in case the user copy/pastes output to a public forum for help.
                            sprintf(ERROR_UNABLE_TO_DELETE_FILE, str_replace([DIR_FS_ADMIN, DIR_FS_CATALOG], ['[admin_directory]/', ''], $current_file))
                        );
                    }
                }
            }
        }

        return !$errorOccurred;
    }
}
