<?php

/**
 *
 * @see XenForo_ControllerAdmin_Warning
 */
class ThemeHouse_AutoWarning_Extend_XenForo_ControllerAdmin_Warning extends XFCP_ThemeHouse_AutoWarning_Extend_XenForo_ControllerAdmin_Warning
{

    public function actionIndex()
    {
        /* @var $response XenForo_ControllerResponse_View */
        $response = parent::actionIndex();

        /* @var $warningModel XenForo_Model_Warning */
        $warningModel = $this->getModelFromCache('XenForo_Model_Warning');

        if ($response instanceof XenForo_ControllerResponse_View) {
            $response->params['autoWarnings'] = $warningModel->prepareAutoWarnings($warningModel->getAutoWarnings());
        }

        return $response;
    } /* END actionIndex */

    protected function _getAutoWarningAddEditResponse(array $autoWarning)
    {
        /* @var $warningModel XenForo_Model_Warning */
        $warningModel = $this->getModelFromCache('XenForo_Model_Warning');

        $warnings = $warningModel->prepareWarningDefinitions($warningModel->getWarningDefinitions());

        $viewParams = array(
            'autoWarning' => $autoWarning,
            'warnings' => $warnings,

            'userCriteria' => XenForo_Helper_Criteria::prepareCriteriaForSelection($autoWarning['user_criteria']),
            'userCriteriaData' => XenForo_Helper_Criteria::getDataForUserCriteriaSelection(),
        );

        return $this->responseView('ThemeHouse_AutoWarning_ViewAdmin_Warning_AutoAdd',
            'th_auto_warning_add_autowarning', $viewParams);
    } /* END _getAutoWarningAddEditResponse */

    public function actionAutoAdd()
    {
        /* @var $warningModel XenForo_Model_Warning */
        $warningModel = $this->getModelFromCache('XenForo_Model_Warning');

        if ($this->_input->filterSingle('fill', XenForo_Input::UINT)) {
            // filler result
            $choice = $this->_input->filterSingle('choice', XenForo_Input::UINT);
            $warning = $warningModel->getWarningDefinitionById($choice);
            if ($warning) {
                $warning = $warningModel->prepareWarningDefinition($warning, true);
            } else {
                $warning = array(
                    'warning_definition_id' => 0,
                    'points_default' => 1,
                    'expiry_type' => 'months',
                    'expiry_default' => 1,
                    'extra_user_group_ids' => '',
                    'is_editable' => 1,
                    'title' => '',
                    'conversationTitle' => '',
                    'conversationMessage' => ''
                );
            }

            return $this->responseView('ThemeHouse_AutoWarning_ViewAdmin_Warning_AutoFill', '',
                array(
                    'warning' => $warning
                ));
        }

        $autoWarning = array(
            'warning_definition_id' => 0,
            'points' => 1,
            'expiry_unit' => 'months',
            'expiry_value' => 1,
            'extra_user_group_ids' => '',
            'is_editable' => 1,
            'title' => '',
            'conversationTitle' => '',
            'conversationMessage' => '',
            'notes' => '',
            'deleteReason' => '',
            'publicWarning' => '',
            'contentTypes' => array(),
            'user_criteria' => array(),
            'page_criteria' => array()
        );

        return $this->_getAutoWarningAddEditResponse($autoWarning);
    } /* END actionAutoAdd */

    public function actionAutoEdit()
    {
        $autoWarningId = $this->_input->filterSingle('auto_warning_id', XenForo_Input::UINT);
        $autoWarning = $this->_getAutoWarningOrError($autoWarningId);

        return $this->_getAutoWarningAddEditResponse($autoWarning);
    } /* END actionAutoEdit */

    public function actionAutoSave()
    {
        $autoWarningId = $this->_input->filterSingle('auto_warning_id', XenForo_Input::UINT);
        $dwInput = $this->_input->filter(
            array(
                'warning_definition_id' => XenForo_Input::UINT,
                'points' => XenForo_Input::UINT,
                'expiry_unit' => XenForo_Input::STRING,
                'expiry_value' => XenForo_Input::UINT,
                'content_action' => XenForo_Input::STRING,
                'open_invite' => XenForo_Input::UINT,
                'conversation_locked' => XenForo_Input::UINT,
                'content_types' => XenForo_Input::ARRAY_SIMPLE,
                'content_criteria' => XenForo_Input::ARRAY_SIMPLE,
                'user_criteria' => XenForo_Input::ARRAY_SIMPLE
            ));
        $phrases = $this->_input->filter(
            array(
                'title' => XenForo_Input::STRING,
                'conversation_title' => XenForo_Input::STRING,
                'conversation_message' => XenForo_Input::STRING,
                'notes' => XenForo_Input::STRING,
                'delete_reason' => XenForo_Input::STRING,
                'public_warning' => XenForo_Input::STRING
            ));

        /* @var $userModel XenForo_Model_User */
        $userModel = $this->getModelFromCache('XenForo_Model_User');
        $user = $userModel->getUserByName($this->_input->filterSingle('username', XenForo_Input::STRING));

        if ($user) {
            $dwInput['user_id'] = $user['user_id'];
        } else {
            return $this->responseError(new XenForo_Phrase('requested_user_not_found'));
        }

        $dw = XenForo_DataWriter::create('ThemeHouse_AutoWarning_DataWriter_AutoWarning');
        if ($autoWarningId) {
            $dw->setExistingData($autoWarningId);
        }
        $dw->bulkSet($dwInput);

        if ($phrases['title']) {
            $dw->setExtraData(ThemeHouse_AutoWarning_DataWriter_AutoWarning::DATA_TITLE, $phrases['title']);
        }
        if ($phrases['conversation_title']) {
            $dw->setExtraData(ThemeHouse_AutoWarning_DataWriter_AutoWarning::DATA_CONVERSATION_TITLE,
                $phrases['conversation_title']);
            $dw->setExtraData(ThemeHouse_AutoWarning_DataWriter_AutoWarning::DATA_CONVERSATION_TEXT,
                $phrases['conversation_message']);
        }
        if ($phrases['notes']) {
            $dw->setExtraData(ThemeHouse_AutoWarning_DataWriter_AutoWarning::DATA_NOTES, $phrases['notes']);
        }
        if ($phrases['delete_reason']) {
            $dw->setExtraData(ThemeHouse_AutoWarning_DataWriter_AutoWarning::DATA_DELETE_REASON,
                $phrases['delete_reason']);
        }
        if ($phrases['public_warning']) {
            $dw->setExtraData(ThemeHouse_AutoWarning_DataWriter_AutoWarning::DATA_PUBLIC_WARNING,
                $phrases['public_warning']);
        }

        $dw->save();

        return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
            XenForo_Link::buildAdminLink('warnings') . '#_auto-' . $dw->get('auto_warning_id'));
    } /* END actionAutoSave */

    /**
     * Deletes a warning action.
     *
     * @return XenForo_ControllerResponse_Abstract
     */
    public function actionAutoDelete()
    {
        if ($this->isConfirmedPost()) {
            return $this->_deleteData('ThemeHouse_AutoWarning_DataWriter_AutoWarning', 'auto_warning_id',
                XenForo_Link::buildAdminLink('warnings'));
        } else {
            $autoWarningId = $this->_input->filterSingle('auto_warning_id', XenForo_Input::UINT);
            $autoWarning = $this->_getAutoWarningOrError($autoWarningId);

            $viewParams = array(
                'autoWarning' => $autoWarning
            );

            return $this->responseView('ThemeHouse_AutoWarning_ViewAdmin_Warning_AutoDelete',
                'th_auto_warning_delete_autowarning', $viewParams);
        }
    } /* END actionAutoDelete */

    /**
     * Gets the specified warning action or throws an exception.
     *
     * @param string $id
     *
     * @return array
     */
    protected function _getAutoWarningOrError($id)
    {
        $result = $this->getRecordOrError($id, $this->_getWarningModel(), 'getAutoWarningById',
            'th_requested_auto_warning_not_found_autowarning');

        return $this->_getWarningModel()->prepareAutoWarning($result, true);
    } /* END _getAutoWarningOrError */
}