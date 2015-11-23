<?php

/**
 *
 * @see XenForo_Route_PrefixAdmin_Warnings
 */
class ThemeHouse_AutoWarning_Extend_XenForo_Route_PrefixAdmin_Warnings extends XFCP_ThemeHouse_AutoWarning_Extend_XenForo_Route_PrefixAdmin_Warnings
{

    /**
     * Match a specific route for an already matched prefix.
     *
     * @see XenForo_Route_Interface::match()
     */
    public function match($routePath, Zend_Controller_Request_Http $request, XenForo_Router $router)
    {
        if (preg_match('#^auto/(.*)$#i', $routePath, $match)) {
            $action = 'auto' . $router->resolveActionWithIntegerParam($match[1], $request, 'auto_warning_id');
            return $router->getRouteMatch('XenForo_ControllerAdmin_Warning', $action, 'userWarnings');
        }

        return parent::match($routePath, $request, $router);
    } /* END match */

    /**
     * Method to build a link to the specified page/action with the provided
     * data and params.
     *
     * @see XenForo_Route_BuilderInterface
     */
    public function buildLink($originalPrefix, $outputPrefix, $action, $extension, $data, array &$extraParams)
    {
        if (preg_match('#^auto/(.*)$#i', $action, $match)) {
            return XenForo_Link::buildBasicLinkWithIntegerParam("$outputPrefix/auto", $match[1], $extension, $data,
                'auto_warning_id');
        }

        return parent::buildLink($originalPrefix, $outputPrefix, $action, $extension, $data, $extraParams);
    } /* END buildLink */
}