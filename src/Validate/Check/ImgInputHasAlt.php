<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Arr;
use Jidaikobo\A11yc\Validate;

class ImgInputHasAlt extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'img_input_has_alt', null, 1, $runtime);
        $str = Element\Get::ignoredHtml($url, false, $context);

        $ms = Element\Get::elementsByRe($str, 'ignores', 'tags', false, $context);
        if (! $ms[0]) {
            return;
        }

        foreach ($ms[0] as $k => $m) {
            if (substr($m, 0, 6) !== '<input') {
                continue;
            }
            $attrs = Element\Get::attributes($m, $context);
            if (! isset($attrs['type'])) {
                continue;
            }
            if (isset($attrs['type']) && $attrs['type'] != 'image') {
                continue;
            }
            $tstr = $ms[0][$k];

            $src = basename(Arr::get($attrs, 'src', ''));

            \Jidaikobo\A11yc\ValidationRecorder::recordErrorOrPass(
                ! isset($attrs['alt']) || empty($attrs['alt']),
                $url,
                'img_input_has_alt',
                $k,
                $tstr,
                $src,
                $runtime
            );
        }
        static::addErrorToHtml($url, 'img_input_has_alt');
    }
}
