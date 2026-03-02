<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Arr;
use Jidaikobo\A11yc\Util;
use Jidaikobo\A11yc\Validate;

class EmptyAltAttrOfImgInsideA extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck(
            $url,
            'empty_alt_attr_of_img_inside_a',
            null,
            1,
            $runtime
        );

        $str = Element\Get::ignoredHtml($url, false, $context);

        $ms = Element\Get::elementsByRe($str, 'ignores', 'anchors_and_values', false, $context);
        if (! $ms[2]) {
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck(
                $url,
                'empty_alt_attr_of_img_inside_a',
                null,
                4,
                $runtime
            );
            return;
        }

        foreach ($ms[2] as $k => $m) {
            if (strpos($m, '<img') === false) {
                continue;
            }
            if (Element::isIgnorable($ms[0][$k])) {
                continue;
            }
            $t = trim(strip_tags($m));
            if (! empty($t)) {
                continue;
            }

            $mms = Element\Get::elementsByRe($m, 'ignores', 'imgs', true, $context);
            $alt = '';
            $src = '';
            foreach ($mms[0] as $in_img) {
                $attrs = Element\Get::attributes($in_img, $context);
                $alt .= Arr::get($attrs, 'alt', '');
                $src = Arr::get($attrs, 'src', '');
            }
            $src = ! empty($src) ? Util::s(basename($src)) : '';

            $alt = trim($alt);
            $tstr = $ms[0][$k];

            if (! $alt) {
                \Jidaikobo\A11yc\ValidationRecorder::error(
                    $url,
                    'empty_alt_attr_of_img_inside_a',
                    $k,
                    $tstr,
                    $src,
                    $runtime
                );
            } else {
                \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck(
                    $url,
                    'empty_alt_attr_of_img_inside_a',
                    $tstr,
                    2,
                    $runtime
                );
            }
        }

        static::addErrorToHtml($url, 'empty_alt_attr_of_img_inside_a');
    }
}
