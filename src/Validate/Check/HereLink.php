<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\RuntimeConfig;
use Jidaikobo\A11yc\Validate;

class HereLink extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'here_link', null, 1, $runtime);
        $str = Element\Get::ignoredHtml($url, false, $context);
        $ms = Element\Get::elementsByRe($str, 'ignores', 'anchors_and_values', false, $context);
        if (! $ms[2]) {
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'here_link', null, 4, $runtime);
            return;
        }

        $heres = array_map(
            'trim',
            explode(',', RuntimeConfig::langConst('A11YC_LANG_HERE', 'here, click here, click'))
        );
        foreach ($ms[2] as $k => $m) {
            \Jidaikobo\A11yc\ValidationRecorder::recordErrorOrPass(
                in_array(strtolower($m), $heres),
                $url,
                'here_link',
                $k,
                $ms[0][$k],
                trim($m),
                $runtime
            );
        }
        static::addErrorToHtml($url, 'here_link');
    }
}
