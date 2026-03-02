<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Arr;
use Jidaikobo\A11yc\Validate;

class AreaHasAlt extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'area_has_alt', null, 1, $runtime);
        $str = Element\Get::ignoredHtml($url, false, $context);

        $ms = Element\Get::elementsByRe($str, 'ignores', 'tags', false, $context);
        if (! $ms[0]) {
            return;
        }
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'area_has_alt', null, 0, $runtime);
        $is_exists = false;

        foreach ($ms[0] as $k => $m) {
            if (substr($m, 0, 5) !== '<area') {
                continue;
            }
            $is_exists = true;

            $attrs = Element\Get::attributes($m, $context);
            $tstr = $ms[0][$k];

            \Jidaikobo\A11yc\ValidationRecorder::recordErrorOrPass(
                ! isset($attrs['alt']) || empty($attrs['alt']),
                $url,
                'area_has_alt',
                $k,
                $tstr,
                basename(Arr::get($attrs, 'href', '')),
                $runtime
            );
        }

        if (! $is_exists) {
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'area_has_alt', null, 4, $runtime);
        }

        static::addErrorToHtml($url, 'area_has_alt');
    }
}
