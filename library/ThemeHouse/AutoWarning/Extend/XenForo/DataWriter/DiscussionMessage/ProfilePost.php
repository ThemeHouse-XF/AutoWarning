<?php

/**
 *
 * @see XenForo_DataWriter_DiscussionMessage_ProfilePost
 */
class ThemeHouse_AutoWarning_Extend_XenForo_DataWriter_DiscussionMessage_ProfilePost extends XFCP_ThemeHouse_AutoWarning_Extend_XenForo_DataWriter_DiscussionMessage_ProfilePost
{

    /**
     *
     * @see XenForo_DataWriter_DiscussionMessage::_messagePreSave()
     */
    protected function _messagePostSave()
    {
        parent::_messagePostSave();

        if ($this->isInsert()) {
            if (!XenForo_Visitor::getInstance()->hasPermission('general', 'bypassAutoWarningCheck')) {
                /* @var $warningModel XenForo_Model_Warning */
                $warningModel = $this->getModelFromCache('XenForo_Model_Warning');

                $warningModel->addToAutoWarningQueue('profile_post', $this->get('profile_post_id'), $this->get('post_date'));
            }
        }
    } /* END _messagePostSave */
}