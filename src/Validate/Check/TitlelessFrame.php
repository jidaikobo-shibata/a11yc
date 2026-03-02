<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Arr;
use Jidaikobo\A11yc\Validate;

class TitlelessFrame extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'titleless_frame', null, 1, $runtime);

        $str = Element\Get::ignoredHtml($url, false, $context);
        $ms = Element\Get::elementsByRe($str, 'ignores', 'tags', false, $context);
        if (! $ms[0]) {
            return;
        }

        foreach ($ms[0] as $k => $v) {
            if ($ms[1][$k] != 'frame' && $ms[1][$k] != 'iframe') {
                continue;
            }
            $attrs = Element\Get::attributes($v, $context);
            $tstr = $ms[0][$k];

            \Jidaikobo\A11yc\ValidationRecorder::recordErrorOrPass(
                ! trim(Arr::get($attrs, 'title')),
                $url,
                'titleless_frame',
                $k,
                '',
                $tstr,
                $runtime
            );
        }
        static::addErrorToHtml($url, 'titleless_frame');
    }
}
