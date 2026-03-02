<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Validate;

class StyleForStructure extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'style_for_structure', null, 1, $runtime);
        $str = Element\Get::ignoredHtml($url, false, $context);

        $ms = Element\Get::elementsByRe($str, 'ignores', 'tags', false, $context);
        if (! $ms[0]) {
            return;
        }

        foreach ($ms[0] as $k => $m) {
            $tstr = $ms[0][$k];
            $attrs = Element\Get::attributes($m, $context);
            if (! array_key_exists('style', $attrs)) {
                continue;
            }

            \Jidaikobo\A11yc\ValidationRecorder::recordErrorOrPass(
                strpos($attrs['style'], 'size') !== false ||
                strpos($attrs['style'], 'color') !== false,
                $url,
                'style_for_structure',
                $k,
                $tstr,
                $m,
                $runtime
            );
        }
        static::addErrorToHtml($url, 'style_for_structure');
    }
}
