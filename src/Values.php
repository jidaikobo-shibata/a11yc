<?php

namespace Jidaikobo\A11yc;

class Values
{
    public static function uas()
    {
        return array(
            'using' => array(
                'name' => RuntimeConfig::langConst('A11YC_LANG_UA_USING', 'Current Browser'),
                'str' => '',
            ),
            'iphone' => array(
                'name' => RuntimeConfig::langConst('A11YC_LANG_UA_IPHONE', 'iPhone'),
                'str' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 10_0 like Mac OS X) ' .
                    'AppleWebKit/602.1.38 (KHTML, like Gecko) Version/10.0 ' .
                    'Mobile/14A300 Safari/602.1',
            ),
            'android' => array(
                'name' => RuntimeConfig::langConst('A11YC_LANG_UA_ANDROID', 'Android'),
                'str' => 'Mozilla/5.0 (Linux; U; Android 2.3.3; ja-jp; ' .
                    'INFOBAR A01 Build/S7142) AppleWebKit/533.1 ' .
                    '(KHTML, like Gecko) Version/4.0 Mobile Safari/533.1',
            ),
            'ipad' => array(
                'name' => RuntimeConfig::langConst('A11YC_LANG_UA_IPAD', 'iPad'),
                'str' => 'Mozilla/5.0 (iPad; CPU OS 10_0 like Mac OS X) ' .
                    'AppleWebKit/602.1.38 (KHTML, like Gecko) Version/10.0 ' .
                    'Mobile/14A300 Safari/602.1',
            ),
            'tablet' => array(
                'name' => RuntimeConfig::langConst('A11YC_LANG_UA_ANDROID_TABLET', 'Android Tablet'),
                'str' => 'Mozilla/5.0 (Android; Tablet; rv:36.0) Gecko/36.0 Firefox/36.0',
            ),
            'featurephone' => array(
                'name' => RuntimeConfig::langConst('A11YC_LANG_UA_FEATUREPHONE', 'Feature Phone'),
                'str' => 'DoCoMo/2.0 SH06A3(c500;TC;W30H18)',
            ),
        );
    }

    public static function targetMimes()
    {
        return array(
            'text/html',
            'application/pdf',
        );
    }

    public static function techsTypes()
    {
        return array('G', 'H', 'C', 'SCR', 'SVR', 'T', 'ARIA', 'PDF', 'F');
    }

    public static function getRefUrls()
    {
        return array(
            0 => array(
                'w' => RuntimeConfig::refWcag20Url(),
                'u' => RuntimeConfig::refWcag20UnderstandingUrl(),
                't' => RuntimeConfig::refWcag20TechUrl(),
            ),
            1 => array(
                'w' => RuntimeConfig::refWcag20Url(),
                'u' => RuntimeConfig::refWcag20UnderstandingUrl(),
                't' => RuntimeConfig::refWcag20TechUrl(),
            ),
        );
    }

    public static function machineCheckStatus()
    {
        return array(
            -1 => RuntimeConfig::langConst('A11YC_LANG_CHECKLIST_MACHINE_CHECK_FAILED', 'failed'),
            1 => RuntimeConfig::langConst('A11YC_LANG_CHECKLIST_MACHINE_CHECK_DONE', 'done'),
            2 => RuntimeConfig::langConst('A11YC_LANG_CHECKLIST_MACHINE_CHECK_PASSED', 'passed'),
            3 => RuntimeConfig::langConst('A11YC_LANG_CHECKLIST_MACHINE_CHECK_EXIST', 'exists'),
            4 => RuntimeConfig::langConst('A11YC_LANG_CHECKLIST_MACHINE_CHECK_NONEXIST', 'does not exist'),
            5 => RuntimeConfig::langConst('A11YC_LANG_CHECKLIST_MACHINE_CHECK_SKIPED', 'skipped'),
        );
    }
}
