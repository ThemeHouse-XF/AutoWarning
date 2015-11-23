<?php

class ThemeHouse_AutoWarning_Install_Controller extends ThemeHouse_Install
{

    protected $_resourceManagerUrl = 'http://xenforo.com/community/resources/automatic-warnings.1957/';

    protected function _getTables()
    {
        return array(
            'xf_auto_warning' => array(
                'auto_warning_id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY', /* END 'auto_warning_id' */
                'warning_definition_id' => 'int(10) unsigned NOT NULL', /* END 'warning_definition_id' */
                'points' => 'smallint(5) unsigned NOT NULL', /* END 'points_default' */
                'expiry_unit' => 'enum(\'never\',\'days\',\'weeks\',\'months\',\'years\') NOT NULL', /* END 'expiry_unit' */
                'expiry_value' => 'smallint(5) unsigned NOT NULL', /* END 'expiry_value' */
                'user_id' => 'int(10) unsigned NOT NULL', /* END 'user_id' */
                'content_action' => 'enum(\'\',\'delete_content\',\'public_warning\') NOT NULL', /* END 'content_action' */
                'open_invite' => 'tinyint(3) unsigned NOT NULL', /* END 'open_invite' */
                'conversation_locked' => 'tinyint(3) unsigned NOT NULL', /* END 'conversation_locked' */
                'content_types' => 'VARCHAR(255) NOT NULL', /* END 'content_types' */
                'content_criteria' => 'mediumblob NOT NULL', /* END 'content_criteria' */
                'user_criteria' => 'mediumblob NOT NULL', /* END 'user_criteria' */
            ), /* END 'xf_auto_warning' */
            'xf_auto_warning_queue' => array(
                'content_type' => 'varchar(25) NOT NULL', /* END 'content_type' */
                'content_id' => 'int(10) unsigned NOT NULL', /* END 'content_id' */
                'content_date' => 'int(10) unsigned NOT NULL DEFAULT \'0\'', /* END 'content_date' */
            ), /* END 'xf_auto_warning_queue' */
        );
    } /* END _getTables */

    protected function _getTableChanges()
    {
        return array(
            'xf_warning' => array(
                'auto_warning_id' => 'int(10) unsigned NOT NULL DEFAULT 0', /* END 'warning_id' */
            ), /* END 'xf_warning' */
        );
    } /* END _getTableChanges */

    protected function _getPrimaryKeys()
    {
        return array(
            'xf_auto_warning_queue' => array(
                'content_type',
                'content_id'
            ), /* END 'xf_auto_warning_queue' */
        );
    } /* END _getPrimaryKeys */

    protected function _getKeys()
    {
        return array(
            'xf_auto_warning' => array(
                'points' => array(
                    'points'
                ), /* END 'points' */
            ), /* END 'xf_auto_warning' */
            'xf_auto_warning_queue' => array(
                'content_date' => array(
                    'content_date'
                ), /* END 'content_date' */
            ), /* END 'xf_auto_warning_queue' */
        );
    } /* END _getKeys */

    protected function _getPermissionEntries()
    {
        return array(
            'general' => array(
                'bypassAutoWarningCheck' => array(
                    'permission_group_id' => 'general', /* 'permission_group_id' */
                    'permission_id' => 'bypassFloodCheck', /* 'permission_id' */
                ), /* 'bypassAutoWarningCheck' */
            ), /* 'general' */
        );
    } /* END _getPermissionEntries */
}