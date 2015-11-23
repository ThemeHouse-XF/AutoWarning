<?php

/**
 *
 * @see XenForo_DataWriter_DiscussionMessage_Post
 */
class ThemeHouse_AutoWarning_Extend_XenForo_DataWriter_DiscussionMessage_Post extends XFCP_ThemeHouse_AutoWarning_Extend_XenForo_DataWriter_DiscussionMessage_Post
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

                $warningModel->addToAutoWarningQueue('post', $this->get('post_id'), $this->get('post_date'));
            }
        }
    } /* END _messagePostSave */
}