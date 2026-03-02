<?php

namespace Jidaikobo\A11yc\Validate\Check;

use Jidaikobo\A11yc\Element;
use Jidaikobo\A11yc\Validate;

class SameAltAndFilenameOfImg extends Validate
{
    public static function check($url, $context = null, $runtime = null)
    {
        \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck(
            $url,
            'same_alt_and_filename_of_img',
            null,
            1,
            $runtime
        );
        $str = Element\Get::ignoredHtml($url, false, $context);
        $ms = Element\Get::elementsByRe($str, 'ignores', 'imgs', false, $context);
        if (! $ms[0]) {
            \Jidaikobo\A11yc\ValidationRecorder::recordMachineCheck(
                $url,
                'same_alt_and_filename_of_img',
                null,
                4,
                $runtime
            );
            return;
        }

        foreach ($ms[0] as $k => $m) {
            $attrs = Element\Get::attributes($m, $context);
            if (! isset($attrs['alt']) || ! isset($attrs['src'])) {
                continue;
            }
            if (empty($attrs['alt'])) {
                continue;
            }
            $tstr = $m;

            $filename = basename($attrs['src']);

            \Jidaikobo\A11yc\ValidationRecorder::recordErrorOrPass(
                $attrs['alt'] == $filename ||
                $attrs['alt'] == substr($filename, 0, strrpos($filename, '.')) ||
                $attrs['alt'] == substr($filename, 0, strrpos($filename, '-')),
                $url,
                'same_alt_and_filename_of_img',
                $k,
                $tstr,
                '"' . $filename . '"',
                $runtime
            );
        }
        static::addErrorToHtml($url, 'same_alt_and_filename_of_img');
    }
}
