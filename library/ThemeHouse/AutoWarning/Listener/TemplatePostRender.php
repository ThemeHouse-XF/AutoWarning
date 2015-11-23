<?php

class ThemeHouse_AutoWarning_Listener_TemplatePostRender extends ThemeHouse_Listener_TemplatePostRender
{

    protected function _getTemplates()
    {
        return array(
            'warning_list'
        );
    } /* END _getTemplates */

    public static function templatePostRender($templateName, &$content, array &$containerData,
        XenForo_Template_Abstract $template)
    {
        $templatePostRender = new ThemeHouse_AutoWarning_Listener_TemplatePostRender($templateName, $content,
            $containerData, $template);
        list($content, $containerData) = $templatePostRender->run();
    } /* END templatePostRender */

    protected function _warningList()
    {
        $viewParams = $this->_fetchViewParams();
        $this->_appendTemplate('th_topctrl_autowarning', $viewParams, $this->_containerData['topctrl']);

        $this->_appendTemplate('th_warning_list_autowarning');
    } /* END _warningList */
}