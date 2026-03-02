<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Arr;
use Jidaikobo\A11yc\Validate;

class AltAttrOfImg extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'alt_attr_of_img', null, 1, $runtime);
        $str = Element\Get::ignoredHtml($url, false, $context);
        $ms = Element\Get::elementsByRe($str, 'ignores', 'imgs', false, $context);

        if (! $ms[1]) {
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'alt_attr_of_img', null, 4, $runtime);
            return;
        }
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'alt_attr_of_img', null, 0, $runtime);

        foreach ($ms[0] as $k => $m) {
            $tstr = $m;
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'alt_attr_of_img', $tstr, 1, $runtime);

            $attrs = Element\Get::attributes($m, $context);
            $file = Arr::get($attrs, 'src');
            $file = ! empty($file) ? basename($file) : $file;

            if (! array_key_exists('alt', $attrs)) {
                \Jidaikobo\A11yc\ValidationRecorder::error($url, 'alt_attr_of_img', $k, $tstr, $file, $runtime);
                continue;
            }
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck($url, 'alt_attr_of_img', $tstr, 2, $runtime);

            if (Arr::get($attrs, 'role') == 'presentation') {
                continue;
            }

            \Jidaikobo\A11yc\ValidationRecorder::recordErrorOrPass(
                preg_match('/^[ 　]+?$/', $attrs['alt']),
                $url,
                'alt_attr_of_blank_only',
                $k,
                $tstr,
                $file,
                $runtime
            );

            \Jidaikobo\A11yc\ValidationRecorder::recordErrorOrPass(
                empty($attrs['alt']),
                $url,
                'alt_attr_of_empty',
                $k,
                $tstr,
                $file,
                $runtime
            );
        }

        static::addErrorToHtml($url, 'alt_attr_of_empty');
        static::addErrorToHtml($url, 'alt_attr_of_img');
        static::addErrorToHtml($url, 'alt_attr_of_blank_only');
    }
}
