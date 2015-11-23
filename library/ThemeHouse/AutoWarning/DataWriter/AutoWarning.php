<?php

/**
 * Data writer for auto warnings.
 */
class ThemeHouse_AutoWarning_DataWriter_AutoWarning extends XenForo_DataWriter
{

    /**
     * Constants for extra data that holds the value for the phrases that are
     * the title, conversation title, conversation text, notes, delete reason
     * and public warning for this auto warning.
     *
     * @var string
     * @var string
     * @var string
     * @var string
     * @var string
     * @var string
     */
    const DATA_TITLE = 'phraseTitle';

    const DATA_CONVERSATION_TITLE = 'phraseConversationTitle';

    const DATA_CONVERSATION_TEXT = 'phraseConversationText';

    const DATA_NOTES = 'phraseNotes';

    const DATA_DELETE_REASON = 'phraseDeleteReason';

    const DATA_PUBLIC_WARNING = 'phrasePublicWarning';

    /**
     * Title of the phrase that will be created when a call to set the
     * existing data fails (when the data doesn't exist).
     *
     * @var string
     */
    protected $_existingDataErrorPhrase = 'th_requested_auto_warning_not_found_autowarning';

    /**
     * Gets the fields that are defined for the table.
     * See parent for explanation.
     *
     * @return array
     */
    protected function _getFields()
    {
        return array(
            'xf_auto_warning' => array(
                'auto_warning_id' => array(
                    'type' => self::TYPE_UINT,
                    'autoIncrement' => true
                ),
                'warning_definition_id' => array(
                    'type' => self::TYPE_UINT,
                    'default' => 0
                ),
                'points' => array(
                    'type' => self::TYPE_UINT,
                    'required' => true,
                    'max' => 65535
                ),
                'expiry_unit' => array(
                    'type' => self::TYPE_STRING,
                    'default' => 'never',
                    'allowedValues' => array(
                        'never',
                        'days',
                        'weeks',
                        'months',
                        'years'
                    )
                ),
                'expiry_value' => array(
                    'type' => self::TYPE_UINT,
                    'default' => 0,
                    'max' => 65535
                ),
                'user_id' => array(
                    'type' => self::TYPE_UINT,
                    'required' => true
                ),
                'content_action' => array(
                    'type' => self::TYPE_STRING,
                    'default' => '',
                    'allowedValues' => array(
                        '',
                        'delete_content',
                        'public_warning'
                    )
                ),
                'open_invite' => array(
                    'type' => self::TYPE_BOOLEAN,
                    'default' => false
                ),
                'conversation_locked' => array(
                    'type' => self::TYPE_BOOLEAN,
                    'default' => false
                ),
                'content_types' => array(
                    'type' => self::TYPE_SERIALIZED,
                    'required' => true
                ),
                'content_criteria' => array(
                    'type' => self::TYPE_SERIALIZED,
                    'required' => true
                ),
                'user_criteria' => array(
                    'type' => self::TYPE_UNKNOWN,
                    'required' => true,
                    'verification' => array(
                        '$this',
                        '_verifyCriteria'
                    )
                )
            )
        );
    } /* END _getFields */

    /**
     * Gets the actual existing data out of data that was passed in.
     * See parent for explanation.
     *
     * @param mixed
     *
     * @return array false
     */
    protected function _getExistingData($data)
    {
        if (!$id = $this->_getExistingPrimaryKey($data)) {
            return false;
        }
        
        return array(
            'xf_auto_warning' => $this->_getWarningModel()->getAutoWarningById($id)
        );
    } /* END _getExistingData */

    /**
     * Gets SQL condition to update the existing record.
     *
     * @return string
     */
    protected function _getUpdateCondition($tableName)
    {
        return 'auto_warning_id = ' . $this->_db->quote($this->getExisting('auto_warning_id'));
    } /* END _getUpdateCondition */

    protected function _preSave()
    {
        if (!$this->get('warning_definition_id') && !$this->getExtraData(self::DATA_TITLE)) {
            $this->error(new XenForo_Phrase('please_enter_valid_title'));
        }
    } /* END _preSave */

    /**
     * Verifies that the criteria is valid and formats is correctly.
     * Expected input format: [] with children: [rule] => name, [data] => info
     *
     * @param array|string $criteria Criteria array or serialize string; see
     * above for format. Modified by ref.
     *
     * @return boolean
     */
    protected function _verifyCriteria(&$criteria)
    {
        $criteriaFiltered = XenForo_Helper_Criteria::prepareCriteriaForSave($criteria);
        $criteria = serialize($criteriaFiltered);
        return true;
    } /* END _verifyCriteria */

    /**
     * Post-save handling.
     */
    protected function _postSave()
    {
        $id = $this->get('auto_warning_id');
        
        $phraseData = array(
            self::DATA_TITLE => $this->_getTitlePhraseName($id),
            self::DATA_CONVERSATION_TITLE => $this->_getConversationTitlePhraseName($id),
            self::DATA_CONVERSATION_TEXT => $this->_getConversationTextPhraseName($id),
            self::DATA_NOTES => $this->_getNotesPhraseName($id),
            self::DATA_DELETE_REASON => $this->_getDeleteReasonPhraseName($id),
            self::DATA_PUBLIC_WARNING => $this->_getPublicWarningPhraseName($id)
        );
        
        foreach ($phraseData as $phraseDataElement => $phraseName) {
            $phraseText = $this->getExtraData($phraseDataElement);
            $this->_insertOrUpdateMasterPhrase($phraseName, $phraseText);
        }
    } /* END _postSave */

    /**
     * Post-delete handling.
     */
    protected function _postDelete()
    {
        $id = $this->get('auto_warning_id');
        
        $this->_deleteMasterPhrase($this->_getTitlePhraseName($id));
        $this->_deleteMasterPhrase($this->_getConversationTitlePhraseName($id));
        $this->_deleteMasterPhrase($this->_getConversationTextPhraseName($id));
        $this->_deleteMasterPhrase($this->_getNotesPhraseName($id));
        $this->_deleteMasterPhrase($this->_getDeleteReasonPhraseName($id));
        $this->_deleteMasterPhrase($this->_getPublicWarningPhraseName($id));
    } /* END _postDelete */

    /**
     * Gets the name of the title phrase for this auto warning.
     *
     * @param string $id
     *
     * @return string
     */
    protected function _getTitlePhraseName($id)
    {
        return $this->_getWarningModel()->getAutoWarningTitlePhraseName($id);
    } /* END _getTitlePhraseName */

    /**
     * Gets the name of the conversation title phrase for this auto warning.
     *
     * @param string $id
     *
     * @return string
     */
    protected function _getConversationTitlePhraseName($id)
    {
        return $this->_getWarningModel()->getAutoWarningConversationTitlePhraseName($id);
    } /* END _getConversationTitlePhraseName */

    /**
     * Gets the name of the conversation text phrase for this auto warning.
     *
     * @param string $id
     *
     * @return string
     */
    protected function _getConversationTextPhraseName($id)
    {
        return $this->_getWarningModel()->getAutoWarningConversationTextPhraseName($id);
    } /* END _getConversationTextPhraseName */

    /**
     * Gets the name of the notes phrase for this auto warning.
     *
     * @param string $id
     *
     * @return string
     */
    protected function _getNotesPhraseName($id)
    {
        return $this->_getWarningModel()->getAutoWarningNotesPhraseName($id);
    } /* END _getNotesPhraseName */

    /**
     * Gets the name of the delete reason phrase for this auto warning.
     *
     * @param string $id
     *
     * @return string
     */
    protected function _getDeleteReasonPhraseName($id)
    {
        return $this->_getWarningModel()->getAutoWarningDeleteReasonPhraseName($id);
    } /* END _getDeleteReasonPhraseName */

    /**
     * Gets the name of the public warning phrase for this auto warning.
     *
     * @param string $id
     *
     * @return string
     */
    protected function _getPublicWarningPhraseName($id)
    {
        return $this->_getWarningModel()->getAutoWarningPublicWarningPhraseName($id);
    } /* END _getPublicWarningPhraseName */

    /**
     *
     * @return XenForo_Model_Warning
     */
    protected function _getWarningModel()
    {
        return $this->getModelFromCache('XenForo_Model_Warning');
    } /* END _getWarningModel */
}