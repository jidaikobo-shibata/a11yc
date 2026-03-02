<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Validate;

class Viewport extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'user_scalable_no', null, 5, $runtime);
        if (Validate::isPartialRun($runtime)) {
            return;
        }
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'user_scalable_no', null, 1, $runtime);

        $str = Element\Get::ignoredHtml($url, false, $context);
        $ms = Element\Get::elementsByRe($str, 'ignores', 'tags', false, $context);
        if (! $ms[0]) {
            return;
        }

        foreach ($ms[1] as $k => $tag) {
            $tstr = $ms[0][$k];
            \Jidaikobo\A11yc\ValidationRecorder::recordErrorOrPass(
                $tag == 'meta' && strpos($ms[2][$k], 'user-scalable=no') !== false,
                $url,
                'user_scalable_no',
                0,
                $tstr,
                'user-scalable=no',
                $runtime
            );
        }
        static::addErrorToHtml($url, 'user_scalable_no');
    }
}
