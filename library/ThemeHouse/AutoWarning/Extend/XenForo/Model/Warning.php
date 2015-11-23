<?php

/**
 *
 * @see XenForo_Model_Warning
 */
class ThemeHouse_AutoWarning_Extend_XenForo_Model_Warning extends XFCP_ThemeHouse_AutoWarning_Extend_XenForo_Model_Warning
{

    public function getAutoWarningById($id)
    {
        return $this->_getDb()->fetchRow(
            '
			SELECT auto_warning.*, user.username
			FROM xf_auto_warning AS auto_warning
            LEFT JOIN xf_user AS user ON (auto_warning.user_id = user.user_id)
			WHERE auto_warning.auto_warning_id = ?
		', $id);
    } /* END getAutoWarningById */

    public function getAutoWarnings()
    {
        return $this->fetchAllKeyed(
            '
			SELECT auto_warning.*, user.username
			FROM xf_auto_warning AS auto_warning
            LEFT JOIN xf_user AS user ON (auto_warning.user_id = user.user_id)
			ORDER BY auto_warning.points
		', 'auto_warning_id');
    } /* END getAutoWarnings */

    public function prepareAutoWarning(array $autoWarning, $includeExtraPhrases = false)
    {
        if ($autoWarning['warning_definition_id']) {
            $autoWarning['title'] = new XenForo_Phrase(
                $this->getWarningDefinitionTitlePhraseName($autoWarning['warning_definition_id']));
        } else {
            $autoWarning['title'] = new XenForo_Phrase(
                $this->getAutoWarningTitlePhraseName($autoWarning['auto_warning_id']));
        }

        if ($includeExtraPhrases) {
            $autoWarning['conversationTitle'] = new XenForo_Phrase(
                $this->getAutoWarningConversationTitlePhraseName($autoWarning['auto_warning_id']));
            $autoWarning['conversationMessage'] = new XenForo_Phrase(
                $this->getAutoWarningConversationTextPhraseName($autoWarning['auto_warning_id']));
            $autoWarning['notes'] = new XenForo_Phrase(
                $this->getAutoWarningNotesPhraseName($autoWarning['auto_warning_id']));
            $autoWarning['deleteReason'] = new XenForo_Phrase(
                $this->getAutoWarningDeleteReasonPhraseName($autoWarning['auto_warning_id']));
            $autoWarning['publicWarning'] = new XenForo_Phrase(
                $this->getAutoWarningPublicWarningPhraseName($autoWarning['auto_warning_id']));
        }

        if ($autoWarning['content_types']) {
            $autoWarning['contentTypes'] = unserialize($autoWarning['content_types']);
        } else {
            $autoWarning['contentTypes'] = array();
        }

        if ($autoWarning['content_criteria']) {
            $autoWarning['contentCriteria'] = unserialize($autoWarning['content_criteria']);
        } else {
            $autoWarning['contentCriteria'] = array();
        }

        return $autoWarning;
    } /* END prepareAutoWarning */

    public function prepareAutoWarnings(array $autoWarnings, $includeExtraPhrases = false)
    {
        foreach ($autoWarnings as &$autoWarning) {
            $autoWarning = $this->prepareAutoWarning($autoWarning, $includeExtraPhrases);
        }

        return $autoWarnings;
    } /* END prepareAutoWarnings */

    public function getAutoWarningTitlePhraseName($id)
    {
        return 'auto_warning_' . $id . '_title';
    } /* END getAutoWarningTitlePhraseName */

    public function getAutoWarningConversationTitlePhraseName($id)
    {
        return 'auto_warning_' . $id . '_conversation_title';
    } /* END getAutoWarningConversationTitlePhraseName */

    public function getAutoWarningConversationTextPhraseName($id)
    {
        return 'auto_warning_' . $id . '_conversation_text';
    } /* END getAutoWarningConversationTextPhraseName */

    public function getAutoWarningNotesPhraseName($id)
    {
        return 'auto_warning_' . $id . '_notes';
    } /* END getAutoWarningNotesPhraseName */

    public function getAutoWarningDeleteReasonPhraseName($id)
    {
        return 'auto_warning_' . $id . '_delete_reason';
    } /* END getAutoWarningDeleteReasonPhraseName */

    public function getAutoWarningPublicWarningPhraseName($id)
    {
        return 'auto_warning_' . $id . '_public_warning';
    } /* END getAutoWarningPublicWarningPhraseName */

    public function getAutoWarningMasterPhraseValues($id)
    {
        $phraseModel = $this->getModelFromCache('XenForo_Model_Phrase');

        return array(
            'title' => $phraseModel->getMasterPhraseValue($this->getAutoWarningTitlePhraseName($id)),
            'conversationTitle' => $phraseModel->getMasterPhraseValue(
                $this->getAutoWarningConversationTitlePhraseName($id)),
            'conversationText' => $phraseModel->getMasterPhraseValue(
                $this->getAutoWarningConversationTextPhraseName($id)),
            'notes' => $phraseModel->getMasterPhraseValue($this->getAutoWarningDeleteReasonPhraseName($id)),
            'deleteReason' => $phraseModel->getMasterPhraseValue($this->getAutoWarningPublicWarningPhraseName($id)),
            'publicWarning' => $phraseModel->getMasterPhraseValue($this->getAutoWarningMasterPhraseValues($id))
        );
    } /* END getAutoWarningMasterPhraseValues */

    public function addToAutoWarningQueue($contentType, $contentId, $contentDate = null)
    {
        if (!$contentDate) {
            $contentDate = XenForo_Application::$time;
        }

        $this->_getDb()->insert('xf_auto_warning_queue',
            array(
                'content_type' => $contentType,
                'content_id' => $contentId,
                'content_date' => $contentDate
            ));
    } /* END addToAutoWarningQueue */

    public function getAllAutoWarningQueueEntries()
    {
        return $this->_getDb()->fetchAll(
            '
			SELECT *
			FROM xf_auto_warning_queue
            ORDER BY content_date ASC
		');
    } /* END getAllAutoWarningQueueEntries */

    public function deleteFromAutoWarningQueue($contentType, $contentId)
    {
        $this->_getDb()->delete('xf_auto_warning_queue',
            'content_type = ' . $this->_getDb()
                ->quote($contentType) . ' AND content_id = ' . $this->_getDb()
                ->quote($contentId));
    } /* END deleteFromAutoWarningQueue */
}