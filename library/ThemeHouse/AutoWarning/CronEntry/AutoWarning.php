<?php

class ThemeHouse_AutoWarning_CronEntry_AutoWarning
{

    protected static $_warnings = null;

    protected static $_handlers = null;

    public static function processAutoWarnings()
    {
        /* @var $warningModel XenForo_Model_Warning */
        $warningModel = XenForo_Model::create('XenForo_Model_Warning');

        $queueEntries = $warningModel->getAllAutoWarningQueueEntries();

        $entries = array();
        foreach ($queueEntries as $queueEntry) {
            $entries[$queueEntry['content_type']][$queueEntry['content_id']] = $queueEntry;
        }

        if (isset($entries['post'])) {
            /* @var $postModel XenForo_Model_Post */
            $postModel = XenForo_Model::create('XenForo_Model_Post');
            $posts = $postModel->getPostsByIds(array_keys($entries['post']),
                array(
                    'join' => XenForo_Model_Post::FETCH_THREAD | XenForo_Model_Post::FETCH_USER
                ));
            foreach ($posts as $postId => $post) {
                $entries['post'][$postId] = array_merge($post, $entries['post'][$postId]);
                $entries['post'][$postId]['content_title'] = $post['title'];
            }
        }

        if (isset($entries['profile_post'])) {
            /* @var $profilePostModel XenForo_Model_Profile_Post */
            $profilePostModel = XenForo_Model::create('XenForo_Model_ProfilePost');
            $profilePosts = $profilePostModel->getProfilePostsByIds(array_keys($entries['profile_post']),
                array(
                    'join' => XenForo_Model_ProfilePost::FETCH_USER_RECEIVER |
                         XenForo_Model_ProfilePost::FETCH_USER_POSTER
                ));
            $profilePostEntries = array();
            foreach ($profilePosts as $profilePostId => $profilePost) {
                $profilePostEntries[$profilePostId] = array_merge($profilePost,
                    $entries['profile_post'][$profilePostId]);
            }
            $entries['profile_post'] = $profilePostEntries;
        }

        $autoWarnings = $warningModel->prepareAutoWarnings($warningModel->getAutoWarnings(), true);

        foreach ($entries as $contentType => $contentTypeEntries) {
            foreach ($autoWarnings as $autoWarning) {
                if (!in_array($contentType, $autoWarning['contentTypes'])) {
                    continue;
                }
                foreach ($contentTypeEntries as $contentId => $entry) {
                    if (ThemeHouse_AutoWarning_Helper_Criteria::contentMatchesCriteria($autoWarning['contentCriteria'],
                        true, $entry['message']) &&
                         XenForo_Helper_Criteria::userMatchesCriteria($autoWarning['user_criteria'], true, $entry)) {
                        self::_createWarning($autoWarning, $entry);
                    }

                    $warningModel->deleteFromAutoWarningQueue($contentType, $contentId);
                }
            }
        }
    } /* END processAutoWarnings */

    protected static function _createWarning($autoWarning, array $content)
    {
        /* @var $warningModel XenForo_Model_Warning */
        $warningModel = XenForo_Model::create('XenForo_Model_Warning');

        if (is_null(self::$_warnings)) {
            self::$_warnings = $warningModel->prepareWarningDefinitions($warningModel->getWarningDefinitions());
        }

        if (is_null(self::$_handlers)) {
            self::$_handlers = $warningModel->getWarningHandlers();
        }

        $warnings = self::$_warnings;
        $warningHandler = self::$_handlers[$content['content_type']];

        if (!$autoWarning['warning_definition_id'] || empty($warnings[$autoWarning['warning_definition_id']])) {
            // custom warning
            $warning = false;
            $extraGroupIds = '';
            $autoWarning['warning_definition_id'] = 0;
        } else {
            $warning = $warnings[$autoWarning['warning_definition_id']];
            $autoWarning['title'] = (string) $warning['title'];
            $extraGroupIds = $warning['extra_user_group_ids'];
        }

        if ($autoWarning['expiry_value']) {
            $autoWarning['expiry_date'] = min(pow(2, 32) - 1,
                strtotime('+' . $autoWarning['expiry_value'] . ' ' . $autoWarning['expiry_unit']));
        }

        $contentTitle = $warningHandler->getContentTitle($content);

        $warning = array(
            'warning_definition_id' => $autoWarning['warning_definition_id'],
            'title' => $autoWarning['title'],
            'points' => $autoWarning['points'],
            'expiry_date' => $autoWarning['expiry_date'],
            'content_type' => $content['content_type'],
            'content_id' => $content['content_id'],
            'content_title' => $contentTitle,
            'user_id' => $content['user_id'],
            'warning_user_id' => $autoWarning['user_id'],
            'extra_user_group_ids' => $extraGroupIds,
            'auto_warning_id' => $autoWarning['auto_warning_id'],
            'notes' => $autoWarning['notes']
        );

        $dw = XenForo_DataWriter::create('XenForo_DataWriter_Warning');
        $dw->bulkSet($warning);
        $dw->setExtraData(XenForo_DataWriter_Warning::DATA_CONTENT, $content);
        switch ($autoWarning['content_action']) {
            case 'public_warning':
                if ($warningHandler->canPubliclyDisplayWarning()) {
                    $dw->setExtraData(XenForo_DataWriter_Warning::DATA_PUBLIC_WARNING, $autoWarning['publicWarning']);
                }
                break;

            case 'delete_content':
                $dw->setExtraData(XenForo_DataWriter_Warning::DATA_DELETION_REASON, $autoWarning['deleteReason']);
                break;
        }
        $dw->save();

        $warning = $dw->getMergedData();

        $replace = array(
            '{title}' => $contentTitle,
            '{url}' => $warningHandler->getContentUrl($content, true),
            '{name}' => $content['username']
        );

        $conversation = array(
            'conversation_title' => strtr((string) $autoWarning['conversationTitle'], $replace),
            'conversation_message' => strtr((string) $autoWarning['conversationMessage'], $replace),
            'conversation_locked' => $autoWarning['conversation_locked'],
            'open_invite' => $autoWarning['open_invite']
        );

        if ($conversation['conversation_title'] && $conversation['conversation_message']) {
            $visitor = XenForo_Visitor::getInstance();

            $user = XenForo_Model::create('XenForo_Model_User')->getUserById($autoWarning['user_id']);

            $conversationDw = XenForo_DataWriter::create('XenForo_DataWriter_ConversationMaster');
            $conversationDw->setExtraData(XenForo_DataWriter_ConversationMaster::DATA_ACTION_USER, $user);
            $conversationDw->setExtraData(XenForo_DataWriter_ConversationMaster::DATA_MESSAGE,
                $conversation['conversation_message']);
            $conversationDw->bulkSet(
                array(
                    'user_id' => $autoWarning['user_id'],
                    'username' => $user['username'],
                    'title' => $conversation['conversation_title'],
                    'open_invite' => $conversation['open_invite'],
                    'conversation_open' => ($conversation['conversation_locked'] ? 0 : 1)
                ));
            $conversationDw->addRecipientUserIds(array(
                $content['user_id']
            ));

            $messageDw = $conversationDw->getFirstMessageDw();
            $messageDw->set('message', $conversation['conversation_message']);
            $conversationDw->save();

            XenForo_Model::create('XenForo_Model_Conversation')->markConversationAsRead(
                $conversationDw->get('conversation_id'), $autoWarning['user_id'], XenForo_Application::$time);
        }
    } /* END _createWarning */
}