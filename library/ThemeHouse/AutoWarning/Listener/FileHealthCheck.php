<?php

class ThemeHouse_AutoWarning_Listener_FileHealthCheck
{

    public static function fileHealthCheck(XenForo_ControllerAdmin_Abstract $controller, array &$hashes)
    {
        $hashes = array_merge($hashes,
            array(
                'library/ThemeHouse/AutoWarning/CronEntry/AutoWarning.php' => 'a2bace237b65b4dd9a10ed7282ccd638',
                'library/ThemeHouse/AutoWarning/DataWriter/AutoWarning.php' => '8fc9aeae5840a562a832c2507feaced9',
                'library/ThemeHouse/AutoWarning/Extend/XenForo/ControllerAdmin/Warning.php' => 'ba81602d8672f76f45879461a655d505',
                'library/ThemeHouse/AutoWarning/Extend/XenForo/DataWriter/DiscussionMessage/Post.php' => '4e5c69e94722ee1a0dfc407d6e8a937e',
                'library/ThemeHouse/AutoWarning/Extend/XenForo/DataWriter/DiscussionMessage/ProfilePost.php' => '12847f9fd8365003d8a82ecedad1ddc3',
                'library/ThemeHouse/AutoWarning/Extend/XenForo/DataWriter/Warning.php' => 'd4a9757485c69760362b192eea7eb1b8',
                'library/ThemeHouse/AutoWarning/Extend/XenForo/Model/Warning.php' => '63f0663caaf31dac3e4c65b4e78714e9',
                'library/ThemeHouse/AutoWarning/Extend/XenForo/Route/PrefixAdmin/Warnings.php' => '57833f333d3d057e73a7a75329120094',
                'library/ThemeHouse/AutoWarning/Helper/Criteria.php' => '4b8ef10ad328a84b68229534d277f700',
                'library/ThemeHouse/AutoWarning/Install/Controller.php' => '2e118bdc995af865fd7bfdbbcdfa8a79',
                'library/ThemeHouse/AutoWarning/Listener/LoadClass.php' => 'b2a1fdfc2f18bb15ee1442fb93dd0c13',
                'library/ThemeHouse/AutoWarning/Listener/TemplatePostRender.php' => '7d54265a02fe39fedd6fc53acca39d58',
                'library/ThemeHouse/AutoWarning/ViewAdmin/Warning/AutoFill.php' => '14e0c72bd73e971fabb99b984d177a38',
                'library/ThemeHouse/Install.php' => '18f1441e00e3742460174ab197bec0b7',
                'library/ThemeHouse/Install/20151109.php' => '2e3f16d685652ea2fa82ba11b69204f4',
                'library/ThemeHouse/Deferred.php' => 'ebab3e432fe2f42520de0e36f7f45d88',
                'library/ThemeHouse/Deferred/20150106.php' => 'a311d9aa6f9a0412eeba878417ba7ede',
                'library/ThemeHouse/Listener/ControllerPreDispatch.php' => 'fdebb2d5347398d3974a6f27eb11a3cd',
                'library/ThemeHouse/Listener/ControllerPreDispatch/20150911.php' => 'f2aadc0bd188ad127e363f417b4d23a9',
                'library/ThemeHouse/Listener/InitDependencies.php' => '8f59aaa8ffe56231c4aa47cf2c65f2b0',
                'library/ThemeHouse/Listener/InitDependencies/20150212.php' => 'f04c9dc8fa289895c06c1bcba5d27293',
                'library/ThemeHouse/Listener/LoadClass.php' => '5cad77e1862641ddc2dd693b1aa68a50',
                'library/ThemeHouse/Listener/LoadClass/20150518.php' => 'f4d0d30ba5e5dc51cda07141c39939e3',
                'library/ThemeHouse/Listener/Template.php' => '0aa5e8aabb255d39cf01d671f9df0091',
                'library/ThemeHouse/Listener/Template/20150106.php' => '8d42b3b2d856af9e33b69a2ce1034442',
                'library/ThemeHouse/Listener/TemplatePostRender.php' => 'b6da98a55074e4cde833abf576bc7b5d',
                'library/ThemeHouse/Listener/TemplatePostRender/20150106.php' => 'efccbb2b2340656d1776af01c25d9382',
            ));
    }
}