<?php

/**
 *
 * @see XenForo_DataWriter_Warning
 */
class ThemeHouse_AutoWarning_Extend_XenForo_DataWriter_Warning extends XFCP_ThemeHouse_AutoWarning_Extend_XenForo_DataWriter_Warning
{

    /**
     *
     * @see XenForo_DataWriter_Warning::_getFields()
     */
    protected function _getFields()
    {
        $fields = parent::_getFields();

        $fields['xf_warning']['auto_warning_id'] = array(
            'type' => XenForo_DataWriter::TYPE_UINT,
            'default' => 0,
        );

        return $fields;
    } /* END _getFields */
}