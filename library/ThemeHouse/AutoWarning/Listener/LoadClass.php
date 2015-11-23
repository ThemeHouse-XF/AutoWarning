<?php

class ThemeHouse_AutoWarning_Listener_LoadClass extends ThemeHouse_Listener_LoadClass
{
    protected function _getExtendedClasses()
    {
        return array(
            'ThemeHouse_AutoWarning' => array(
                'controller' => array(
                    'XenForo_ControllerAdmin_Warning',
                ), /* END 'controller' */
                'route_prefix' => array(
                    'XenForo_Route_PrefixAdmin_Warnings',
                ), /* END 'route_prefix' */
                'model' => array(
                    'XenForo_Model_Warning',
                ), /* END 'model' */
                'datawriter' => array(
                    'XenForo_DataWriter_DiscussionMessage_Post',
                    'XenForo_DataWriter_DiscussionMessage_ProfilePost',
                    'XenForo_DataWriter_Warning',
                ), /* END 'datawriter' */
            ), /* END 'ThemeHouse_AutoWarning' */
        );
    } /* END _getExtendedClasses */

    public static function loadClassController($class, array &$extend)
    {
        $loadClassController = new ThemeHouse_AutoWarning_Listener_LoadClass($class, $extend, 'controller');
        $extend = $loadClassController->run();
    } /* END loadClassController */

    public static function loadClassRoutePrefix($class, array &$extend)
    {
        $loadClassRoutePrefix = new ThemeHouse_AutoWarning_Listener_LoadClass($class, $extend, 'route_prefix');
        $extend = $loadClassRoutePrefix->run();
    } /* END loadClassRoutePrefix */

    public static function loadClassModel($class, array &$extend)
    {
        $loadClassModel = new ThemeHouse_AutoWarning_Listener_LoadClass($class, $extend, 'model');
        $extend = $loadClassModel->run();
    } /* END loadClassModel */

    public static function loadClassDataWriter($class, array &$extend)
    {
        $loadClassDataWriter = new ThemeHouse_AutoWarning_Listener_LoadClass($class, $extend, 'datawriter');
        $extend = $loadClassDataWriter->run();
    } /* END loadClassDataWriter */
}