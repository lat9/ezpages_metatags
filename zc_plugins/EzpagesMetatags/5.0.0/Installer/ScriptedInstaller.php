<?php

    use Zencart\PluginSupport\ScriptedInstaller as ScriptedInstallBase;

    class ScriptedInstaller extends ScriptedInstallBase
    {
        protected function executeInstall()
        {
            $this->removePrepluginVersion();
            $this->tableCheckup();
            $this->insertConstants();
        }

        protected function insertConstants(): void
        {
        }

        protected function executeUninstall()
        {
            $keys = [
            ];
//            $this->deleteConfigurationKeys($keys);
        }

        private function sniffTable(): void
        {
        }

        protected function tableCheckup(): void
        {
            global $db, $sniffer;

            if (!$sniffer->field_exists(TABLE_EZPAGES_CONTENT, 'pages_meta_title')) {
                $db->Execute(
                    "ALTER TABLE " . TABLE_EZPAGES_CONTENT . "
            ADD pages_meta_title VARCHAR(255) NOT NULL DEFAULT '',
            ADD pages_meta_keywords text,
            ADD pages_meta_description text"
                );
            }
        }

        protected function removePrepluginVersion(): void
        {
        }
    }
